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

set_time_limit(0);

/**
    * Classe responsável por trabalhar com os layouts de exportação de dados
    * @author Analista/Desenvolvedor: Diego Barbosa Victoria
    * @package Exportador
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
class Exportador
{
/**
    * @access Private
    * @var Array
*/
var $arArquivos;
/**
    * @access Private
    * @var String
*/
var $stNomeArquivoZip = '';
/**
    * @access Private
    * @var Object
*/
var $roUltimoArquivo;

/**
    * @access Private
    * @var Object
*/
var $obTConfiguracao;
/**
    * @access Private
    * @var String
*/
var $pgRetorno;

/**
    * Método Construtor
    * @access Private
*/
function Exportador()
{
    $this->arArquivos = array();
    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");
    $this->obTConfiguracao    = new TAdministracaoConfiguracao()   ;
    $this->setRetorno('LSExportacao.php');
}

function setRetorno($valor) { $this->pgRetorno = $valor; }
function getRetorno() { return $this->pgRetorno;   }

function setNomeArquivoZip($valor) { $this->stNomeArquivoZip = $valor ; }
function getNomeArquivoZip() { return $this->stNomeArquivoZip   ; }

function addArquivo($stNome)
{
    include_once ( CLA_ARQUIVO_EXPORTADOR );
    $this->arArquivos[]     = new ArquivoExportador( $this , $stNome );
    $this->roUltimoArquivo  = &$this->arArquivos[ count( $this->arArquivos ) -1 ];
}

function Show()
{
    $arArquivosDownload;
    include_once ( CLA_ARQUIVO_ZIP );
    include_once ( CLA_ERRO );
    $obErro             = new Erro              ;
    $obArquivoZip       = new ArquivoZip        ;
    $flMicroTime        = SistemaLegado::getmicrotime()        ;
    $inContador         = 0                     ;

    //Recuperando parametro garbage colector de Arquivos e excluindo arquivos
    //com data anterior a (data atual - parametro)

    $this->obTConfiguracao->pegaConfiguracao($inGCArq,"gc_arq");

    $dtLimite = mktime (0, 0, 0, date("m"), date("d")-$inGCArq,  date("Y"));
    $flMicroTimeDel = $dtLimite;
    $stCaminho = CAM_FRAMEWORK.'tmp/';

    if ($stHandle = opendir($stCaminho)) {
        while (false !== ($stFile = readdir($stHandle))) {
            $inPos = strpos($stFile,'_');
            $flFileTimestamp = substr($stFile,0,$inPos);
            if ($flFileTimestamp<$flMicroTimeDel AND strlen($stFile)>=10) {
                $stComando = 'rm -rf ' . $stCaminho . $stFile;
                exec($stComando);
            }
        }
    }

    /*
    foreach ($arFiles as $stFile) {
        $inPos = strpos($stFile,'_');
        $flFileTimestamp = substr($stFile,0,$inPos);
        if ($flFileTimestamp<$flMicroTimeDel) {
            $stComando = 'rm -rf ' . $stCaminho . $stFile;
            echo $stComando;
            //exec($stComando);
        }
    }
    */
    foreach ($this->arArquivos as $obArquivo) {
        // Monta Localização

        $stNomeArquivo      = $flMicroTime.'_'.$obArquivo->stNome;
        $stLabelZip         = $obArquivo->stNome;
        $obArquivo->stNome  = $stCaminho.$stNomeArquivo;

        $obErro = $obArquivo->Gravar();
        $arArquivosDownload[$inContador]['stNomeArquivo'] = $stLabelZip       ;
        $arArquivosDownload[$inContador]['stLink'       ] = $obArquivo->stNome;
        $inContador = $inContador+1;
        // se nome terminar com .zip insere o arquivo corrente
        if (preg_match("/.zip$/i",$this->getNomeArquivoZip())) {
            $obArquivoZip->AdicionarArquivo($obArquivo->stNome,$stLabelZip);
        }

        if($obErro->ocorreu())
            break;
    }
    if (preg_match("/.zip$/i",$this->getNomeArquivoZip())) {
            $stNomeZip = $obArquivoZip->Show();
            $arArquivosDownload = array();
            $arArquivosDownload[0]['stNomeArquivo'] = $this->getNomeArquivoZip();
            $arArquivosDownload[0]['stLink'       ] = $stCaminho.$stNomeZip;
        }
    // MAnda array de arquivos para a sessao
    Sessao::write('arArquivosDownload',$arArquivosDownload);
    SistemaLegado::alertaAviso($this->getRetorno(),"Arquivo(s) Gerados com Sucesso","incluir","aviso", Sessao::getId(), "../");

    return $obErro;
}
}
?>
