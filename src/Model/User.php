<?php

namespace App\Model;

use App\Core\Database;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use DateTime;

#[Entity]
class User
{

    #[Id, Column(name: "id"), GeneratedValue]
    private int $id;

    // Nível de acesso: 'user' (padrão) ou 'admin'
    #[Column(name: "nivelAcesso", options: ["default" => "user"])]
    private string $nivelAcesso;

    #[Column]
    private string $nome;

    #[Column (unique: true)]
    private string $email;

    #[Column]
    private string $senha;  // Guardará o hash da senha, nunca a senha pura.

    #[Column(type: "datetime")]
    private DateTime $dataCadastro;

    


    public function __construct(string $nome, string $email, string $senhaPura)
    {
        $this->nome = $nome;
        $this->email = $email;

        $this->setSenha($senhaPura); // Chama o método que CRIA O HASH

        // Define a data de cadastro para o momento exato da criação do objeto.
        $this->dataCadastro = new DateTime();

        // Por padrão, todo novo usuário é comum
        $this->nivelAcesso = 'user';
    }

    public function setSenha(string $senhaPura): void
    {
        // A mágica do hash seguro acontece aqui.
        $this->senha = password_hash($senhaPura, PASSWORD_DEFAULT);
    }

    //FUNÇÃO PARA VERIFICAR SE A SENHA PURA (FORNECIDA PELO FORMULARIO DE LOGIN) CORRESPONDE AO HASH ARMAZENADO
     public function verificaSenha(string $senhaPura): bool
    {
        return password_verify($senhaPura, $this->senha);
    }

    public function getId(): int {return $this->id;}

	public function getNome(): string {return $this->nome;}

	public function getEmail(): string {return $this->email;}

	public function getSenha(): string {return $this->senha;}

	public function getDataCadastro(): DateTime {return $this->dataCadastro;}

    // Formata a data de cadastro no padrão brasileiro por conveniência
    public function getDataCadastroBR(): string {
        return $this->dataCadastro->format('d-m-Y');
    }
    public function getDataCadastroBRComHora(): string {
        return $this->dataCadastro->format('d-m-Y H:i');
    }

    public function getNivelAcesso(): string { return $this->nivelAcesso; }
    public function isAdmin(): bool { return strtolower($this->nivelAcesso) === 'admin'; }

	// Persiste a entidade no banco usando o EntityManager do Doctrine
    public function save(): void
    {
        $entityManager = Database::getEntityManager();
        $entityManager->persist($this);
        $entityManager->flush();
    }

    /*observações
    
    diferença de $senha e $senhaPura:

    - $senhaPura é a chave, é o texto que o user digita no formulário de login, tipo "minhasenha123"
    ela só existe no momento do login, nunca é armazenada no banco.

    - $senha é o hash gerado a partir do $senhaPura, é o que está armazenado no banco de dados.
    


    CADASTRO DO USUÁRIO

    - Quando o user se cadastro, ele fornece a $senhapura,
    $senhapura = $_POST['senha'] // minhasenha123

    - Voce cria um user passando essa senhapura
    $novoUsuario = new User("Nome do Usuário", "email@email.com", $senhaPura);

    - Dentro do construtor, voce chama o método setSenha que gera o hash e atribui a $senha
    $this->senha = password_hash($senhaPura, PASSWORD_DEFAULT);



    LOGIN DO USUÁRIO
    
    - O User preenche o formulário de login com a senha pura
    $senhapura = $_POST['senha'] // minhasenha123

    - Voce busca o usuário no banco pelo email dele. Este objeto $usuario que vem do banco tem o hash armazenado na propriedade $senha

    - Este é o passo crucial, voce não cria um hash novo, voce usa o metodo verificaSenha para comparar a senhapura com o hash armazenado
    if ($usuario->verificaSenha($senhaPura)) {
        // Senha correta, login ok
    } else {
        // Senha incorreta
    }

    - O que o método verificaSenha() faz por baixo dos panos? Ele usa a função password_verify(). Essa função é o "teste":
        Ela pega a chave que o usuário te deu ($senhaPuraDoLogin 🗝️).
        Ela pega a fechadura que estava na porta ($this->senha 🔐).
        Ela faz o teste e retorna true se a chave abrir a fechadura, ou false se não abrir.
    
    */

}
