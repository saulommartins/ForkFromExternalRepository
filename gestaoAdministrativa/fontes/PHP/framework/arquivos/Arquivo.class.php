<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
* Classe para trabalhar com arquivos
* Data de Criação: 12/01/2005

* @author Analista/Desenvolvedor: Diego Barbosa Victoria

* @package Framework
* @subpackage Arquivos

$Revision: 4009 $
$Name$
$Author: fernando $
$Date: 2005-12-16 14:18:50 -0200 (Sex, 16 Dez 2005) $

Casos de uso: uc-01.01.00

*/

/**
* Classe para trabalhar com arquivos
* @author Analista/Desenvolvedor: Diego Barbosa Victoria
* @package Framework
* @subpackage Arquivos
*/
class Arquivo
{
    /**
    * @access Private
    * @var Resource
    */
    public $reArquivo;

    /**
    * @access Private
    * @var String
    */
    public $stTipo;

    /**
    * @access Private
    * @var String
    */
    public $stNome;

    /**
    * @access Private
    * @var String
    */
    public $stLabel;

    /**
    * @access Private
    * @var String
    */
    public $stConteudo;

    /**
    * @access Private
    * @var Object
    */
    public $obErro;

    /**
    * @access Public
    * @param String $valor
    */
    public function setTipo($valor) { $this->stTipo       = $valor; }
    /**

    * @access Public
    * @param String $valor
    */
    public function setConteudo($valor) { $this->stConteudo   = $valor; }

    /**
    * @access Public
    * @Return String
    */
    public function getTipo() { return $this->stTipo;       }

    /**
    * @access Public
    * @Return String
    */
    public function getConteudo() { return $this->stConteudo;   }

    /**
    * @access Public
    * @Return String
    */
    public function getDiretorio() { return substr($this->stNome,0,strrpos($this->stNome,basename($this->stNome))); }

    /**
    * @access Public
    * @Return Integer
    */
    public function getTamanho() { return file_exists($this->stNome) ? filesize($this->stNome) : ''; }
    /**
    * @access Public
    * @Return String
    */
    public function getNomeArquivo() { return (strpos($this->stNome,"/")===false) ? ($this->stNome) : (substr($this->stNome,strrpos($this->stNome,"/")+1,strlen($this->stNome)-strrpos($this->stNome,"/"))); }

    /**
    * Método Construtor
    * @access Private
    */
    public function Arquivo($stNome , $stLabel = NULL)
    {
        $this->stLabel      = $stLabel;
        $this->stNome       = $stNome;
        $this->stTipo       = "application/force-download";
        $this->obErro       = new Erro;
        $this->stConteudo   = null;
    }

    public function Abrir($stModo)
    {
        $stModo = strtolower(trim($stModo));
        if (!$this->reArquivo = @fopen($this->stNome, $stModo)) {
            $this->obErro->setDescricao("Arquivo (".$this->stNome.") não pôde ser aberto.");
        }

        return $this->obErro;
    }

    public function Fechar()
    {
        if (!fclose($this->reArquivo)) {
            $this->obErro->setDescricao("Arquivo (".$this->stNome.") não pôde ser fechado.");
        }

        return $this->obErro;
    }

    public function Gravar($stModo = 'w+')
    {
        $this->Abrir( $stModo );
        if (!$this->obErro->ocorreu()) {
            if ( fwrite($this->reArquivo, $this->stConteudo) === false) {
                $this->obErro->setDescricao("Arquivo (".$this->stNome.") não pôde ser escrito.");
            }
            if (!$this->obErro->ocorreu()) {
                $this->Fechar();
            }
        }

        return $this->obErro;
    }

    public function Ler($stModo = 'r')
    {
        $this->Abrir( $stModo );
        if (!$this->obErro->ocorreu()) {
            $this->stConteudo = fread($this->reArquivo, $this->getTamanho() );
            $this->Fechar();
        }

        return $this->obErro;
    }

    public function Show()
    {
        header('Content-Description: File Transfer');
        header('Content-Type: '   . $this->stTipo);
        header('Content-Length: ' . $this->getTamanho() );
        // se label estiver setado, passa como nome do arquivo de download
        if (!is_null($this->stLabel)) {
            header('Content-Disposition: attachment; filename=' . $this->stLabel);
        } else {
            header('Content-Disposition: attachment; filename=' . basename($this->stNome));
        }
        if ( @readfile($this->stNome) === false ) {
            $this->obErro->setDescricao("Erro ao tentar exibir o conteúdo do arquivo (".$this->stNome.").");
        }

        return $this->obErro;
    }
}
?>
