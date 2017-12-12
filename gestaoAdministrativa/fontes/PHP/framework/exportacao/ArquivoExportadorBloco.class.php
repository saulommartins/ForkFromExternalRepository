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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

/**
    * Classe para trabalhar com arquivos texto no exportador
    * @author Analista/Desenvolvedor: Diego Barbosa Victoria
    * @package Exportador
*/
class ArquivoExportadorBloco
{
/**
    * @access Private
    * @var String
*/
var $stDelimitador;
/**
    * @access Private
    * @var Object
*/
var $rsRecordSet;
/**
    * @access Private
    * @var Array
*/
var $arLinhas;
/**
    * @access Private
    * @var Array
*/
var $arColunas;
/**
    * @access Private
    * @var Object
*/
var $roUltimaColuna;
/**
    * @access Private
    * @var Object
*/
var $roArquivo;

/**
    * @access Public
    * @param String $valor
*/
function setDelimitador($valor) { $this->stDelimitador= $valor; }

/**
    * @access Public
    * @Return String
*/
function getDelimitador() { return $this->stDelimitador;}

/**
    * Método Construtor
    * @access Private
*/
function ArquivoExportadorBloco(&$roArquivo, &$rsRecordSet)
{
    $this->roArquivo        = $roArquivo;
    $this->rsRecordSet      = $rsRecordSet;
    $this->stDelimitador    = '';
    $this->arLinhas         = array();
}

function addColuna($stCampo)
{
    include_once ( CLA_ARQUIVO_EXPORTADOR_BLOCO_COLUNA );
    $this->arColunas[]      = new ArquivoExportadorBlocoColuna( $this );
    $this->roUltimaColuna   = &$this->arColunas[ count( $this->arColunas ) -1 ];
    $this->roUltimaColuna->setCampo( $stCampo );
}

function addLinha($stCampo)
{
    $this->arLinhas[]   = $stCampo;
}

function Formatar()
{
    if ( strtolower( get_class($this->rsRecordSet) ) != "recordset") {
        $this->roArquivo->obErro->setDescricao("Não foi setado um Recordset válido.");
    } else {
        $this->rsRecordSet->setPrimeiroElemento();
        while (!$this->rsRecordSet->eof()) {
            $stColuna = '';
            for ($inCount=0; $inCount<count($this->arColunas); $inCount++) {
                $stColuna .= $this->arColunas[$inCount]->Formatar();
                switch ( trim($this->roArquivo->getTipoDocumento()) ) {
                
                case "TCE_MG":
                        $stColuna .= $this->stDelimitador;                    
                break;
            
                case "TCM_GO":
                        //Padrão de Saída do Arquivo TCE_GO em ISO-8859-1
                        $stColuna = mb_convert_encoding($stColuna, "ISO-8859-1");                    
                break;

                default:
                    if ( $inCount != (count($this->arColunas)-1) ) {
                        $stColuna .= $this->stDelimitador;
                    }
                break;
            }
                
            }
            //echo "<pre>";print_r($stColuna);echo"</pre>";
            $this->addLinha($stColuna);
            $this->rsRecordSet->proximo();
        }
    }

    return $this->roArquivo->obErro;
}

}
?>
