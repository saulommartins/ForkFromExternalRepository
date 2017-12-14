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
 
    * Página de processamento para baixa de debito automatica
    * Data de criação : 24/03/2006

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Marcelo B. Paulino

    * $Id: PRManterBaixaAutomatica.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.10
**/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php"                                           );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma    = "ManterBaixaAutomatica";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

$obErro          = new Erro;
$obRARRPagamento = new RARRPagamento;

switch ($stAcao) {
    case "incluir":
        if ( file_exists( $_FILES['stArquivoBaixa']['tmp_name'] ) ) {
            if (
                $_FILES['stArquivoBaixa']['type'] != "text/plain" AND
                $_FILES['stArquivoBaixa']['type'] != "application/octet-stream" AND
                $_FILES['stArquivoBaixa']['type'] != "chemical/x-mopac-input"
            ) {
                $obErro->setDescricao( "Formato inválido do arquivo de baixa." );
            } else {
                //verifica o layout do arquivo de baixa
                $obRARRPagamento->setArquivo( $_FILES['stArquivoBaixa'] );
                $stMd5sum = md5_file( $_FILES['stArquivoBaixa']['tmp_name'] );
                $obErro = $obRARRPagamento->verificaLayout( $stLayout, $arDadosBaixa, $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $stClass = "RARR".$stLayout;
                    include_once( CAM_GT_ARR_NEGOCIO.$stClass.".class.php" );
                    $obRLayout = new $stClass;

                    //verificar se arquivo ja foi baixado!
                    $stHeader = $arDadosBaixa[0];
                    $stFooter = $arDadosBaixa[count($arDadosBaixa)-2];
                    $stMd5sum = $stMd5sum;

                    $obRLayout->stMd5Sum = $stMd5sum;
                    $obRLayout->stHeader = trim($stHeader);
                    $obRLayout->stFooter = trim($stFooter);
                    $obRLayout->stNomArquivo = strtoupper( trim($_FILES['stArquivoBaixa']['name']) );

                    $obErro = $obRLayout->listaVerificaMd5($rsMd5,$boTransacao);
                    if ( $rsMd5->getNumLinhas() > 0 ) {
                        $obErro->setDescricao( "Arquivo já baixado!(".$_FILES['stArquivoBaixa']['name'].")" );
                    } else {
                        $obErro = $obRLayout->listaVerificaConteudo($rsConteudo,$boTransacao);
                        if ( $rsConteudo->getNumLinhas() > 0  ) {
                            $obErro->setDescricao( "Arquivo já baixado!(".$_FILES['stArquivoBaixa']['name'].")" );
                        }
                    }

                    if ( !$obErro->ocorreu() )
                        $obErro = $obRLayout->efetuarBaixa( $arDadosBaixa, $boTransacao );
                }
            }
        } else {
            $obErro->setDescricao( "Não foi possível importar o arquivo! (".$_FILES['stArquivoBaixa']['name'].")." );
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso( "FMResumoBaixaAutomatica.php?stAcao=incluir&cod_lote=".$obRLayout->inCodLote."&exercicio=".$obRLayout->stExercicio,"Arquivo: ".$_FILES['stArquivoBaixa']['name'],"incluir","aviso", Sessao::getId(), "../" );
        } else {
            SistemaLegado::exibeAviso(urlencode( $obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../" );
            sistemaLegado::LiberaFrames();
        }
    break;
}
