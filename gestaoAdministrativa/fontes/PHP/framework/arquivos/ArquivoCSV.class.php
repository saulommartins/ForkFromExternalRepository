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

$Revision: 4083 $
$Name$
$Author: fernando $
$Date: 2005-12-19 17:29:08 -0200 (Seg, 19 Dez 2005) $

Casos de uso: uc-01.01.00

*/

/**
* Classe para trabalhar com arquivos
* @author Analista/Desenvolvedor: Diego Barbosa Victoria
* @package Framework
* @subpackage Arquivos
*/
include_once 'ArquivoTexto.class.php';
class ArquivoCSV  extends ArquivoTexto
{
    /**
    * @access Private
    * @var String
    */
    public $stDelimitadorColuna;
    /**
    * @access Private
    * @var String
    */
    public $stDelimitadorTexto;

    /**
    * @access Private
    * @var Object
    */
    public $inTamanhoLinha;

    /**
    * @access Public
    * @param String $valor
    */
    public function setDelimitadorColuna($valor) { $this->stDelimitadorColuna= $valor; }

    /**
    * @access Public
    * @param String $valor
    */
    public function setDelimitadorTexto($valor) { $this->stDelimitadorTexto= $valor; }

    /**
    * @access Public
    * @param String $valor
    */
    public function setTamanhoLinha($valor) { $this->inTamanhoLinha= $valor; }

    /**
    * @access Public
    * @Return String
    */
    public function getDelimitadorColuna() { return $this->stDelimitadorColuna; }

    /**
    * @access Public
    * @Return String
    */
    public function getDelimitadorTexto() { return $this->stDelimitadorTexto; }

    /**
    * @access Public
    * @Return String
    */
    public function getTamanhoLinha() { return $this->inTamanhoLinha; }

    /**
    * Método Construtor
    * @access Private
    */
    public function ArquivoCSV($stNome)
    {
        parent::ArquivoTexto($stNome);
        $this->inTamanhoLinha = 4096;
        $this->stNome         = $stNome;
        $this->stDelimitadorTexto = "\"";
        $this->stDelimitadorColuna = ";";
    }

    public function LerLinha()
    {
        $arLinha = fgetcsv ($this->reArquivo, $this->inTamanhoLinha,$this->stDelimitadorColuna ,$this->stDelimitadorTexto);

        return $arLinha;
    }

}
?>
