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

$Id: ArquivoExportadorBlocoColuna.class.php 65928 2016-06-30 17:33:37Z franver $

Casos de uso: uc-01.01.00
*/

/**
    * Classe para trabalhar as colunas de cada arquivo do exportador
    * @author Analista/Desenvolvedor: Diego Barbosa Victoria
    * @package Exportador
*/
class ArquivoExportadorBlocoColuna
{
/**
    * @access Private
    * @var String
*/
var $stCampo;
/**
    * @access Private
    * @var String
*/
var $stTipoDado;
/**
    * @access Private
    * @var String
*/
var $stAlinhamento;
/**
    * @access Private
    * @var String
*/
var $stMascara;
/**
    * @access Private
    * @var Integer
*/
var $inTamanhoFixo;
/**
    * @access Private
    * @var Integer
*/
var $inTamanhoMaximo;
/**
    * @access Private
    * @var Object
*/
var $roBloco;

/**
    * @access Public
    * @param String $valor
*/
function setCampo($valor) { $this->stCampo          = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTipoDado($valor) { $this->stTipoDado       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setAlinhamento($valor) { $this->stAlinhamento    = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara        = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setPreenchimento($valor) { $this->stPreenchimento  = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setTamanhoFixo($valor) { $this->inTamanhoFixo    = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setCampoObrigatorio($valor) { $this->boCampoObrigatorio = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setTamanhoMaximo($valor) { $this->inTamanhoMaximo  = $valor; }

/**
    * @access Public
    * @Return String
*/
function getCampo() { return $this->stCampo;      }
/**
    * @access Public
    * @Return String
*/
function getTipoDado() { return $this->stTipoDado;   }
/**
    * @access Public
    * @Return String
*/
function getAlinhamento() { return $this->stAlinhamento;}
/**
    * @access Public
    * @Return String
*/
function getMascara() { return $this->stMascara;    }
/**
    * @access Public
    * @Return String
*/
function getPreenchimento() { return $this->stPreenchimento; }
/**
    * @access Public
    * @Return Integer
*/
function getTamanhoFixo() { return $this->inTamanhoFixo;}
/**
    * @access Public
    * @Return Integer
*/
function getTamanhoMaximo() { return $this->inTamanhoMaximo;}
/**
    * @access Public
    * @Return Boolean
*/
function getCampoObrigatorio() { return $this->boCampoObrigatorio;}

/**
    * Método Construtor
    * @access Private
*/
function ArquivoExportadorBlocoColuna(&$roArquivoExportadorBloco)
{
    $this->roBloco              = &$roArquivoExportadorBloco;
    $this->stTipoDado           = null;
    $this->inTamanhoFixo        = null;
    $this->inTamanhoMaximo      = null;
    $this->stCampo              = null;
    $this->stMascara            = null;
    $this->stPreenchimento      = ' ';
    $this->stAlinhamento        = 'E';
    $this->boCampoObrigatorio   = true;
}

function FormataTipoDado($stCampo)
{
    switch ( strtoupper(trim($this->stTipoDado)) ) {
        case "CARACTER_ESPACOS_ESQ":
            $this->stAlinhamento    = 'D';
            $this->stPreenchimento  = ' ';
            $stCampo = str_replace("\r\n"," ",$stCampo);
            $stCampo = str_replace("\n"," ",$stCampo);            
            $stCampo = str_replace(chr(13).chr(10)," ",$stCampo);
            $stCampo = str_replace(chr(10)," ",$stCampo);
            
            switch ( trim($this->roBloco->roArquivo->getTipoDocumento()) ) {
                case "TCE_MG":
                    $stCampo = preg_replace ("[;]", "", $stCampo);        
                    $this->stPreenchimento  = ' ';
                    
                    if ( $stCampo == '' ) {
                        $stCampo = ' ';
                    } else if ( $stCampo == null ) {
                        $stCampo = ' ';
                    }
                    
                break;
            }
        break;
        case "CARACTER_ESPACOS_DIR":
            $this->stAlinhamento    = 'E';
            $this->stPreenchimento  = ' ';
            $stCampo = preg_replace('/[\\n\\r]/',' ',$stCampo);
            $stCampo = str_replace("\r\n"," ",$stCampo);
            $stCampo = str_replace('\n',' ',$stCampo);
            $stCampo = str_replace(chr(10),' ',$stCampo);
            $stCampo = utf8_decode($stCampo);
            $stCampo = str_replace(chr(146),' ',$stCampo);
            $stCampo = str_replace(chr(150),' ',$stCampo);

            switch ( trim($this->roBloco->roArquivo->getTipoDocumento()) ) {
                case "TCE_MG":
                    $stCampo = preg_replace ("[;]", "", $stCampo);
                    $this->stPreenchimento  = ' ';
                    
                    if ( $stCampo == '' ) {
                        $stCampo = ' ';
                    } else if ( $stCampo == null ) {
                        $stCampo = ' ';
                    }
                    
                break;
                case "TCE_PE":
                    $stCampo = str_replace( chr(34) ,"",$stCampo); // retira aspas duplas
                    $stCampo = str_replace( chr(39) ,"",$stCampo); // retira aspas simples ou apóstrofes
                break;
                case "TCM_BA":
                    $stCampo = str_ireplace( array("select","insert","update","delete","drop","xp_"), " ", $stCampo);
                    $stCampo = str_replace( array("'",';','--') ," ",$stCampo);
                break;
            }
        break;
        case "ALFANUMERICO_ESPACOS_DIR":
            $this->stAlinhamento    = 'E';
            $this->stPreenchimento  = ' ';
            $stCampo = preg_replace('/[\\n\\r]/',' ',$stCampo);
            $stCampo = str_replace("\r\n"," ",$stCampo);
            $stCampo = str_replace("\n"," ",$stCampo);
            $stCampo = str_replace(chr(10)," ",$stCampo);
            switch ( trim($this->roBloco->roArquivo->getTipoDocumento()) ) {
                case "TCM_BA":
                    $stCampo = str_ireplace( array("select","insert","update","delete","drop","xp_"), " ", $stCampo);
                    $stCampo = str_replace( array("'",';','--') ," ",$stCampo);
                break;
            default:
                    $stCampo = utf8_decode($stCampo);
                    $stCampo = preg_replace ("/[.|,|;|\/|-]/", " ", $stCampo);
                    $stCampo = str_replace('  ', ' ', $stCampo);
                break;
            }

        break;
        case "CARACTER_ZEROS_ESQ":
            $this->stAlinhamento    = 'D';
            $this->stPreenchimento  = '0';
            $stCampo = str_replace("\r\n"," ",$stCampo);
            $stCampo = str_replace("\n"," ",$stCampo);
            $stCampo = str_replace(chr(10)," ",$stCampo);
            
            switch ( trim($this->roBloco->roArquivo->getTipoDocumento()) ) {
                case "TCE_MG":
                    $stCampo = preg_replace ("[;]", "", $stCampo);
                    if ( $stCampo == '' ) {
                        $stCampo = ' ';
                    } else if ( $stCampo == null ) {
                        $stCampo = ' ';
                    }
                break;
            }
        break;
        case "NUMERICO_ZEROS_ESQ":
            $this->stAlinhamento    = 'D';
            $this->stPreenchimento  = '0';
            switch ( trim($this->roBloco->roArquivo->getTipoDocumento()) ) {
                case "TCE_RS":
                $stCampo = str_replace(" ","",preg_replace ("/[![:alpha:]|.|,|\/]/", "", $stCampo));
                    if ($stCampo<0) {
                        $stCampo = preg_replace ("/[^[:digit:]]/", "", $stCampo);
                        $this->stPreenchimento  = '-' . str_pad($this->stPreenchimento,$this->inTamanhoFixo,'0',STR_PAD_LEFT);
                    }
                break;
            
                default:
                    $stCampo = number_format((float) $stCampo,0,'.','');
                    $stCampo = str_replace(" ","",preg_replace ("^[![:alpha:]|.|,|/]^", "", $stCampo));
                    if ($stCampo<0) {
                        $stCampo = preg_replace ("/[^[:digit:]]/", "", $stCampo);
                        $this->stPreenchimento  = '-' . str_pad($this->stPreenchimento,$this->inTamanhoFixo - 1,'0',STR_PAD_LEFT);
                    }
                break;
            }
        break;
        case "NUMERICO_ZEROS_DIR":
            $this->stAlinhamento    = 'E';
            $this->stPreenchimento  = '0';
            switch ( trim($this->roBloco->roArquivo->getTipoDocumento()) ) {
                case "TCE_RS":
                $stCampo = str_replace(" ","",preg_replace ("[![:alpha:]|.|,|/]", "", $stCampo));
                    if ($stCampo<0) {
                        $stCampo = preg_replace ("[^[:digit:]]", "", $stCampo);
                        $this->stPreenchimento  = '-' . str_pad($this->stPreenchimento,$this->inTamanhoFixo,'0',STR_PAD_LEFT);
                    }
                break;

                default:
                    $stCampo = number_format((float) $stCampo,0,'.','');
                    $stCampo = str_replace(" ","",preg_replace("/[![:alpha:]|.|,|\/]/", "", $stCampo));
                    if ($stCampo<0) {
                        $stCampo = preg_replace("/[^[:digit:]]/", "", $stCampo);
                        $this->stPreenchimento  = '-' . str_pad($this->stPreenchimento,$this->inTamanhoFixo - 1,'0',$this->stAlinhamento,STR_PAD_LEFT);
                    }
                break;
            }
        break;
        case "NUMERICO_ESPACOS_ESQ":
            $this->stAlinhamento    = 'D';
            $this->stPreenchimento  = ' ';
            switch ( trim($this->roBloco->roArquivo->getTipoDocumento()) ) {
                case "TCM_BA":
                    $stCampo = str_replace (",", "", $stCampo);
                    break;
            }
        break;
        case "VALOR_ZEROS_ESQ":
            $this->stAlinhamento    = 'D';
            $this->stPreenchimento  = '0';
            switch ( trim($this->roBloco->roArquivo->getTipoDocumento()) ) {
                case "transparencia":
                    $stCampo = str_replace (".", "", $stCampo);
                break;  
                
                case "TCM_BA":
                    $stCampo = str_replace (".", "", $stCampo);
                    if ($stCampo<0) {
                        if ($this->stAlinhamento=='D') {
                            $this->stAlinhamento= STR_PAD_LEFT;
                        }
                        $stCampo = str_replace ("-", "", $stCampo);
                        $this->stPreenchimento  = '-' . str_pad($this->stPreenchimento,$this->inTamanhoFixo -1 ,'0',$this->stAlinhamento);
                    }
                break;

                case "TCE_PE":
                    if(!$this->getCampoObrigatorio() ){
                        if($stCampo == 0.00){
                            $this->stPreenchimento  = ' ';
                            $stCampo = " ";
                        }else{
                            $stCampo = str_replace (".", ",", $stCampo);
                        }
                    } else {
                        $stCampo = str_replace (".", ",", $stCampo);
                    }
                break;

                case "TCE_RN":
                    $stCampo = str_replace (".", "", $stCampo);
                    if ($stCampo<0) {
                        if ($this->stAlinhamento=='D') {
                            $this->stAlinhamento= STR_PAD_LEFT;
                        }
                        $stCampo = str_replace ("-", "", $stCampo);
                        $this->stPreenchimento  = '-' . str_pad($this->stPreenchimento,$this->inTamanhoFixo -1 ,'0',$this->stAlinhamento);
                    }
                break;
                case 'TCE_MG':
                    $stCampo = str_replace (".", ",", $stCampo);
                break;
                case 'TCE_MG_SIACE':
                    $stCampo = ($stCampo=='') ? '0.00' : $stCampo;
                    $stCampo = str_replace("-","",(str_replace(".", "", $stCampo)));
                break;

                case 'TCE_AM':
                    $stCampo = number_format((float) $stCampo,2,',','');
                break;
                
                case 'TCM_GO':
                    $stCampo = str_replace(" ","",str_replace (".", ",", $stCampo));
                    if ($stCampo<0) {
                        $stCampo = str_replace('-','',$stCampo);
                    }
                break;
                
                default:
                    $stCampo = str_replace(" ","",str_replace (".", ",", $stCampo));
                    if ($stCampo<0) {
                                $stCampo = str_replace('-','',$stCampo);
                                if ($this->stAlinhamento=='D') {
                                    $this->stAlinhamento= STR_PAD_LEFT;
                                    }
                        $this->stPreenchimento  = '-' . str_pad($this->stPreenchimento,$this->inTamanhoFixo -1 ,'0',$this->stAlinhamento);
                    }
                break;
            }
        break;
        case "DATA_DDMMYYYY":
               switch ( trim($this->roBloco->roArquivo->getTipoDocumento()) ) {
                    case "manad":
                        $this->inTamanhoFixo    =null;

                    break;
                    case "TCE_MG":
                        $this->stPreenchimento  = ' ';

                        if ( $stCampo == '' ) {
                            $stCampo = ' ';
                        } else if ( $stCampo == null ) {
                            $stCampo = ' ';
                        }

                    break;
                    default:
                         $this->inTamanhoFixo    = 8;

                    break;
               }

               if(strpos($stCampo,'-') !== FALSE || strpos($stCampo,'/') !== FALSE ){
                    if (strpos($stCampo,'-') !== FALSE) {
                         $arData = explode('-',$stCampo);

                         if (strlen($arData[0]) == 4) {
                             $stCampo = SistemaLegado::dataToBr($stCampo);
                         }
                    }

                    $this->inTamanhoMaximo  = null;
                    $arCampo = explode("/",trim($stCampo));
                    $stCampo = implode("",$arCampo);
               }
        break;
        case "DATA_YYYYMMDD":
            $this->inTamanhoFixo    = 8;
            $this->inTamanhoMaximo  = null;
            $arCampo = explode("/",trim($stCampo));
            $stCampo = $arCampo[2].$arCampo[1].$arCampo[0];
            
            switch ( trim($this->roBloco->roArquivo->getTipoDocumento()) ) {
                case "TCM_GO":
                    $this->stPreenchimento  = ' ';
                    
                    if ( $stCampo == '' ) {
                        $stCampo = ' ';
                    } else if ( $stCampo == null ) {
                        $stCampo = ' ';
                    }
                    
                break;
            }
            
        break;
    }

    return $stCampo;
}

function Formatar()
{
    $stCampo = '';
    if (!$this->stCampo && $this->boCampoObrigatorio) {
        $this->roBloco->roArquivo->obErro->setDescricao('Deve ser setada a coluna');
    } else {
        //$stCampo = $this->roBloco->rsRecordSet->getCampo( $this->stCampo );
        $stCampo = $this->roBloco->rsRecordSet->retornaValoresRecordSet( $this->stCampo );
        $stCampo = $this->FormataTipoDado( $stCampo );
        if ($this->inTamanhoFixo  && !$this->inTamanhoMaximo) {
            switch (trim(strtoupper($this->stAlinhamento))) {
                case 'E':
                case 'L':
                    $this->stAlinhamento = STR_PAD_RIGHT;
                break;
                case 'D':
                case 'R':
                    $this->stAlinhamento = STR_PAD_LEFT;
                break;
                case 'C':
                case 'M':
                case 'B':
                case 'STR_PAD_BOTH':
                    $this->stAlinhamento = STR_PAD_BOTH;
                break;
            }

            switch (trim($this->roBloco->roArquivo->getTipoDocumento())) {
                case 'TCM_BA':
                    if ( mb_strlen($stCampo) > $this->inTamanhoFixo ) {
                        $stCampo = mb_substr($stCampo,0,$this->inTamanhoFixo );
                    }
                break;
                
                default:
                    if ( strlen($stCampo) > $this->inTamanhoFixo ) {
                        $stCampo = substr($stCampo,0,$this->inTamanhoFixo );
                    }
                break;
            }

            $stCampo = str_pad($stCampo,$this->inTamanhoFixo,$this->stPreenchimento,$this->stAlinhamento);
            
        } elseif (!$this->inTamanhoFixo && $this->inTamanhoMaximo) {
            if ( strlen($stCampo) > $this->inTamanhoMaximo ) {
                $stCampo = substr($stCampo,0,$this->inTamanhoMaximo);
            }
        }
    }

    return $stCampo;
}

}
?>
