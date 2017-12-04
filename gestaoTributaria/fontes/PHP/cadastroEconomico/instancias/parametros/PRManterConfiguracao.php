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
    * Página de Processamento ConfiguracaoCEM
    * Data de Criação   : 23/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Vitor Davi Valentini

    * @ignore

    * $Id: PRManterConfiguracao.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.01

*/

/*
$Log$
Revision 1.6  2007/03/28 19:15:49  dibueno
Bug #8633#

Revision 1.5  2006/09/15 14:33:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php");

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRCEMConfiguracao = new RCEMConfiguracao;
$obErro  = new Erro;

switch ($stAcao) {

    case "alterar":

        switch ($_REQUEST["inNumeroLicenca" ]) {
            case "Automatico":
                 $inNumeroLicenca = 0;
                break;
            case "Manual":
                 $inNumeroLicenca = 1;
                break;
            case "Exercicio":
                 $inNumeroLicenca = 2;
            break;
        }
        $boNumeroInscricao = 't';

        if ($_REQUEST["boNumeroInscricao"] == "Manual") {
            $boNumeroInscricao = 'f';
        }

        if ( ( $inNumeroLicenca == 1 ) && ( $_REQUEST["stMascaraLicenca"  ] == "" ) ) {
            $obErro->setDescricao( "Máscara de licença inválida." );
        }

        if ( ( $boNumeroInscricao == 'f' ) && ( $_REQUEST["stMascaraInscricao"] == "" ) ) {
            $obErro->setDescricao( "Máscara de inscrição inválida." );
        }

        if ( !$obErro->ocorreu() ) {
            $obRCEMConfiguracao->setNroAlvara           ( $_REQUEST["stAlvaraLicenca"  ]  );
            $obRCEMConfiguracao->setNumeroLicenca       ( $inNumeroLicenca                );
            $obRCEMConfiguracao->setMascaraLicenca      ( $_REQUEST["stMascaraLicenca"  ] );
            $obRCEMConfiguracao->setNumeroInscricao     ( $boNumeroInscricao              );
            $obRCEMConfiguracao->setMascaraInscricao    ( $_REQUEST["stMascaraInscricao"] );
            $obRCEMConfiguracao->setCNAE                ( $_REQUEST["boCNAE"]             );
            $obRCEMConfiguracao->setCGMDiretorTributos  ( $_REQUEST['inCGM']              );
            $obRCEMConfiguracao->setAnoExercicio        ( Sessao::getExercicio()          );
            $obRCEMConfiguracao->setVgSanitDepartamento ( $_REQUEST['stVgSanitDepartamento'] );
            $obRCEMConfiguracao->setVgSanitSecretaria   ( $_REQUEST['stVgSanitSecretaria'] );
            $obRCEMConfiguracao->setEmissaoCertidaoBaixa( $_REQUEST["stEmissaoCertidaoBaixa"] );
            $obErro = $obRCEMConfiguracao->alterarConfiguracao();
        }

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm,"Configuração","alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }

    break;
}
?>
