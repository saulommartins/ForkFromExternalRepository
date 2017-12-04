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
    * Página de processamento do formulário da Gráfica

    * Data de Criação   : 26/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: PRManterGrafica.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.03
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_FIS_MAPEAMENTO."TFISGrafica.class.php"                                           );
include_once( CAM_GT_FIS_MAPEAMENTO."TFISAutorizacaoNotas.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterGrafica";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

;
Sessao::setTrataExcecao( true );

switch ($_REQUEST['stAcao']) {
    case 'excluir':
        $obTGrafica = new TFISGrafica();
        $obTFISAutorizacaoNotas = new TFISAutorizacaoNotas();
        $rsRecordSet = new RecordSet();

        $inNumCgm   = $_REQUEST['numcgm'];

        $stCaminho = $pgList."?".Sessao::getId()."&stAcao=excluir";

        Sessao::getTransacao()->setMapeamento( $obTGrafica );

        $stCondicao = " WHERE numcgm = " . $inNumCgm;
        $obTFISAutorizacaoNotas->recuperaTodos( $rsRecordSet, $stCondicao );

        if ( $rsRecordSet->Eof() ) {
               $obTGrafica->setDado( "numcgm", $inNumCgm );
            $obTGrafica->exclusao();
            sistemaLegado::alertaAviso($stCaminho,$inNumCgm ,"excluir","aviso",Sessao::getId(),"../");
        } else {
            sistemaLegado::alertaAviso($stCaminho,"Erro ao Deletar Gráficas(Há notas relacionadas com essa Gráfica(".$inNumCgm."))" ,"erro","erro",Sessao::getId(),"../");
        }
        break;
    case 'alterar':
        $obTGrafica = new TFISGrafica();
        $inNumCgm   = $_REQUEST['inCGM'];
        Sessao::getTransacao()->setMapeamento( $obTGrafica );

        $obTGrafica->setDado( "numcgm", $inNumCgm                                          );
        $obTGrafica->setDado( "ativo" , ( $_REQUEST['boAtivo']=="Sim" ) ? "true" : "false" );
        $obTGrafica->alteracao();

        sistemaLegado::alertaAviso($pgList , $inNumCgm ,"alterar","aviso", Sessao::getId(), "../");
        break;
    case 'incluir':
        $obTGrafica = new TFISGrafica();
        $inNumCgm   = $_REQUEST['inCGM'];
        Sessao::getTransacao()->setMapeamento( $obTGrafica );

        $rsRecordSetGrafica = new RecordSet();

        $obTGrafica->setDado( "numcgm", $inNumCgm );
        $obTGrafica->recuperaPorChave( $rsRecordSetGrafica );

        if ($rsRecordSetGrafica->Eof()) {
            $obTGrafica->setDado( "numcgm", $inNumCgm                                          );
            $obTGrafica->setDado( "ativo" , ( $_REQUEST['boAtivo']=="Sim" ) ? "true" : "false" );
            $obTGrafica->inclusao();
            sistemaLegado::alertaAviso($pgForm , $inNumCgm ,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso( "Gráfica já cadastrada para esse CGM.(".$inNumCgm.")","n_incluir","erro" );
        }
        break;
}
Sessao::encerraExcecao();
