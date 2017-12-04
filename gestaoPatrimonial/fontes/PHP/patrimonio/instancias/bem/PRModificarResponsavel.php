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
    * Data de Criação: 24/09/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Grasiele Torres

    $Id: $

    * Casos de uso: uc-03.01.24
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemResponsavel.class.php" );

$stPrograma = "ModificarResponsavel";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTPatrimonioBemResponsavel = new TPatrimonioBemResponsavel();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioBemResponsavel );

switch ($stAcao) {
case 'rescindir' :
    $obTPatrimonioBemResponsavel->recuperaMaxDtInicio( $dt_inicio );

    if (( implode('-',array_reverse(explode('/',$_REQUEST['dtInicioResponsavel'])))) < ($dt_inicio->getCampo('dt_inicio')) ) {
        $stMensagem = 'A data de início deve ser igual ou maior que a data de início do responsável anterior';
    }

    if ( implode('',array_reverse(explode('/',$_REQUEST['dtInicioResponsavel']))) > date('Ymd') ) {
        $stMensagem = 'A data de início do novo responsável deve ser menor ou igual a data de hoje';
    }

    $obTPatrimonioBemResponsavel->recuperaTodos($rsResponsavel, ' WHERE numcgm = '.$_REQUEST['inNumResponsavelAnterior'].'
                                                                        AND dt_fim IS NULL
                                                                        AND timestamp = (SELECT max(timestamp)
                                                                                           FROM patrimonio.bem_responsavel br
                                                                                          WHERE br.cod_bem = bem_responsavel.cod_bem)');
    if (!$stMensagem) {

        if (( implode('-',array_reverse(explode('/',$_REQUEST['dtInicioResponsavel'])))) == ($dt_inicio->getCampo('dt_inicio')) ) {
            $datafim = $_REQUEST['dtInicioResponsavel'];
        } else {
            list($dia, $mes, $ano) = explode('/', $_REQUEST['dtInicioResponsavel']);
            $datafim = date("d/m/Y", mktime (0, 0, 0, $mes  , $dia-1, $ano));
        }

        while ( !$rsResponsavel->eof() ) {
            //Seta a dt_fim do responsável anterior
            $obTPatrimonioBemResponsavel->setDado( 'cod_bem'  , $rsResponsavel->getCampo( 'cod_bem'   ) );
            $obTPatrimonioBemResponsavel->setDado( 'numcgm'   , $rsResponsavel->getCampo( 'numcgm'    ) );
            $obTPatrimonioBemResponsavel->setDado( 'timestamp', $rsResponsavel->getCampo( 'timestamp' ) );
            $obTPatrimonioBemResponsavel->setDado( 'dt_inicio', $rsResponsavel->getCampo( 'dt_inicio' ) );
            $obTPatrimonioBemResponsavel->setDado( 'dt_fim'   , $datafim);
            $obTPatrimonioBemResponsavel->alteracao();

            //Passa todos os bens do responsável anterior para o novo responsável
            $obTPatrimonioBemResponsavel->setDado( 'cod_bem', $rsResponsavel->getCampo( 'cod_bem'   ) );
            $obTPatrimonioBemResponsavel->setDado( 'timestamp', '' );
            $obTPatrimonioBemResponsavel->setDado( 'numcgm',  $_REQUEST['inNumResponsavelNovo'] );
            $obTPatrimonioBemResponsavel->setDado( 'dt_inicio', $_REQUEST['dtInicioResponsavel'] );
            $obTPatrimonioBemResponsavel->setDado( 'dt_fim', '' );
            $obTPatrimonioBemResponsavel->inclusao();

        $rsResponsavel->proximo();
        }

        $stMensagem = "Modificar Responsável concluído com sucesso!";
        SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=rescindir",$stMensagem,'aviso','aviso',Sessao::getId(), "../");

        if ($_REQUEST['boEmitirTermo'] == 'true') {

            if ($rsResponsavel->getNumLinhas() > 0) {

            $stCaminho = CAM_GP_PAT_INSTANCIAS."relatorio/OCGeratermoResponsabilidade.php";
            $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inNumResponsavel=".$_REQUEST['inNumResponsavelNovo']."&stNomResponsavel=".$_REQUEST['stNomResponsavelNovo']."&setPDF=true";
            if (isset($_REQUEST['demo_valor'])) {
                $stCampos .= "&demo_valor=1";
            }

             SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."&acao=2183','oculto');" );

            }
        }
    } else {
        SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
    }
    break;
}

Sessao::encerraExcecao();
