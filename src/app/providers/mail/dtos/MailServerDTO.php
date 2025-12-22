<?php declare(strict_types=1);

namespace App\Providers\Mail\Dtos;

// Native

use App\Support\Facades\Logger;
use InvalidArgumentException;

class MailServerDTO {

	/**
   * Hostname of the mail server
	 * 
	 * @private Hostname
   * @public hostname
	 * @var string|null
	 */
	private string|null $Hostname;
  public string|null $hostname {
    get => $this->Hostname;
    set => $this->Hostname = $value;
  }

	/**
   * Host (IP or hostname) of the mail server
   * 
	 * @private Host
   * @public host
	 * @var string|null
	 */
	public string|null $Host;
  public string|null $host {
    get => $this->Host;
    set => $this->Host = $value;
  }

	/**
   * Helo domain
   * 
	 * @private Helo
   * @public helo
	 * @var string|null
	 */
	public string|null $Helo;
  public string|null $helo {
    get => $this->Helo;
    set => $this->Helo = $value;
  }

	/**
   * Username
   * @private Username
   * @public username
	 * @var string|null
	 */
	public string|null $Username;
  public string|null $username {
    get => $this->Username;
    set => $this->Username = $value;
  }

	/**
	 * Password
   * @private Password
   * @public password
	 * @var string|null
	 */
	public string|null $Password;
  public string|null $password {
    get => $this->Password;
    set => $this->Password = $value;
  }

	/**
	 * Port
   * @private Port
   * @public port
	 * @var int|string|null
	 */
	public int|string|null $Port;
  public int|string|null $port {
    get => $this->Port;
    set => $this->Port = $value;
  }

  public function __construct(
    ?string $hostname     = NULL,
    ?string $host         = NULL,
    ?string $helo         = NULL,
    ?string $username     = NULL,
    ?string $pwd          = NULL,
    int|string|null $port = NULL,
    ?array $dataConn      = NULL
  ) {
    $this->hostname = $dataConn['Hostname'] ?? $hostname;
    $this->host     = $dataConn['Host']     ?? $host;
    $this->helo     = $dataConn['Helo']     ?? $helo;
    $this->username = $dataConn['Username'] ?? $username;
    $this->password = $dataConn['Password'] ?? $pwd;
    $this->port     = $dataConn['Port']     ?? $port;

    $this->Validate();
  }

  private function Validate(): bool {
    if (!!!$this->IsValidHost())
      throw new InvalidArgumentException("+ERROR+MailServeDTO construct IP+ " . date('d/m/Y H:i:s') . " - The specified IP host {".($this->host??"NULL")."} Mail is not valid");
    if (!!!$this->IsValidPort())
      throw new InvalidArgumentException("+ERROR+MailServeDTO construct PORT+ " . date('d/m/Y H:i:s') . " - The specified Port {".($this->port??"NULL")."} is not valid");
    if (empty($this->username))
      throw new InvalidArgumentException("+ERROR+MailServeDTO construct USERNAME+ " . date('d/m/Y H:i:s') . " - Username is required [username:{".$this->username."}]");
    if (empty($this->password))
      throw new InvalidArgumentException("+ERROR+MailServeDTO construct PASSWORD+ " . date('d/m/Y H:i:s') . " - Password is required [password:{".(empty($this->password)?"NULL":str_repeat("*", strlen($this->password)))."}]");

    return TRUE;
  }

  /**
   * Validate IPv4 or IPv6 address.
   * 
   * @param ?string $ip
   * @return bool
   */
  private function IsValidIP(?string $ip = NULL): bool {
    $ip ??= $this->host;

    return (bool)filter_var($ip, FILTER_VALIDATE_IP);
  }

  /**
   * Validate hostname, domain or subdomain.
   *
   * @param string|null $host
   * @return bool
   */
  private function IsValidHostname(?string $host = NULL): bool {
    $host ??= $this->host;

    // Total length according to RFC: 1–253 chars
    $len = strlen($host);
    if ($len < 1 || $len > 253)
      return FALSE;

    return (bool) preg_match(
      '/^(?=.{1,253}$)(?!-)[a-zA-Z0-9-]{1,63}(?<!-)(\.(?!-)[a-zA-Z0-9-]{1,63}(?<!-))*$/',
      $host
    );
  }

  /**
   * Check if $name is a valid hostname, IP or domain, or subdomain name
   * 
   * @param ?string $name
   * @return bool
   */
  private function IsValidHost(?string $name = NULL): bool {
    $name ??= $this->host;

    if (empty($name)) return FALSE;

    return $this->IsValidIP($name) || $this->IsValidHostname($name);
  }

  /**
   * Validate IP port
   * 
   * @param int|string|null $port
   * @return bool
   */
  private function IsValidPort(int|string|null $port = NULL): bool {
    $port ??= $this->port;

    if (!!!is_numeric($port)) 
      return FALSE;

    $port = (int)$port;

    return $port > 0 && $port <= 65535;
  }

  /**
   * Check if the data server mail is setup properly
   * 
   * @param bool $chkHost
   * @param bool $chkUsername
   * @param bool $chkPwd
   * @param bool $chkPort
   * @return bool
   */
  public function IsReady(
    ?bool $chkHost = FALSE, 
    ?bool $chkUsername = FALSE, 
    ?bool $chkPwd = FALSE, 
    ?bool $chkPort = FALSE
  ): bool {
    if(!!!$chkHost && !!!$chkUsername && !!!$chkPwd && !!!$chkPort)
      return $this->Validate();

    if ($chkHost && $this->IsValidHost($this->host))
      return FALSE;
    if ($chkUsername && empty($this->username))
      return FALSE;
    if ($chkPwd && empty($this->password))
      return FALSE;
    if ($chkPort && !!!$this->IsValidPort())
      return FALSE;

    return TRUE;
  }
}
