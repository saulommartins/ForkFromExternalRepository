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
    * PÃ¡gina de Processamento
    * Data de CriaÃ§Ã£o   : 25/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Id: PRManterOrgao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTGO."TTGOOrgao.class.php");
include_once(TTGO."TTGOOrgaoGestor.class.php");
include_once(TTGO."TTGOOrgaoControleInterno.class.php");
include_once(TTGO."TTGOOrgaoRepresentante.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterOrgao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao ( true );
$stAcao = $request->get('stAcao');

$arGestor = Sessao::read('arGestor');

switch ($_REQUEST['stAcao']) {

    case 'incluir':
        if ( count($arGestor) > 0 ) {
            $obTTGOOrgao = new TTGOOrgao();
            $obTTGOOrgaoGestor = new TTGOOrgaoGestor();
            $obTTGOOrgao->setDado( 'num_orgao', $_REQUEST['inOrgao'] );
            $obTTGOOrgao->setDado( 'exercicio', Sessao::getExercicio() );
            $obTTGOOrgao->setDado( 'numcgm_orgao', $_REQUEST['inCGMOrgao'] );
            $obTTGOOrgao->setDado( 'numcgm_contador', $_REQUEST['inCGMContador'] );
            $obTTGOOrgao->setDado( 'cod_tipo', $_REQUEST['inTipoOrgao'] );
            $obTTGOOrgao->setDado( 'crc_contador', $_REQUEST['stCRCContador'] );
            $obTTGOOrgao->setDado( 'uf_crc_contador', $_REQUEST['stSiglaUF'] );
            $obTTGOOrgao->recuperaPorChave( $rsOrgao );
            if ( $rsOrgao->getNumLinhas() > 0 ) {
                $obTTGOOrgao->alteracao();
            } else {
                $obTTGOOrgao->inclusao();
            }

            $obTTGOOrgaoGestor->setDado( 'exercicio', Sessao::getExercicio()      );
            $obTTGOOrgaoGestor->setDado( 'num_orgao', $_REQUEST['inOrgao']    );
            $obTTGOOrgaoGestor->exclusao();
            foreach ($arGestor as $arGestor) {
                $obTTGOOrgaoGestor->setDado( 'exercicio', Sessao::getExercicio()      );
                $obTTGOOrgaoGestor->setDado( 'num_orgao', $_REQUEST['inOrgao']    );
                $obTTGOOrgaoGestor->setDado( 'cargo',     $arGestor['stCargoGestor'] );
                $obTTGOOrgaoGestor->setDado( 'dt_inicio', $arGestor['dtInicio']   );
                $obTTGOOrgaoGestor->setDado( 'dt_fim'   , $arGestor['dtTermino']  );
                $obTTGOOrgaoGestor->setDado( 'numcgm'   , $arGestor['inCGMGestor']);
                $obTTGOOrgaoGestor->inclusao();
            }
            if ($_REQUEST['inCGMReponsavelInterno'] != '') {
                $obTTGOOrgaoControleInterno = new TTGOOrgaoControleInterno();
                $obTTGOOrgaoControleInterno->setDado( 'exercicio', Sessao::getExercicio() );
                $obTTGOOrgaoControleInterno->setDado( 'num_orgao', $_REQUEST['inOrgao'] );
                $obTTGOOrgaoControleInterno->setDado( 'numcgm'   , $_REQUEST['inCGMReponsavelInterno'] );
                $obTTGOOrgaoControleInterno->recuperaPorChave( $rsOrgaoControleInterno );
                if ( $rsOrgaoControleInterno->getNumLinhas() > 0 ) {
                    $obTTGOOrgaoControleInterno->alteracao();
                } else {
                    $obTTGOOrgaoControleInterno->inclusao();
                }

            }

            $obTTGOOrgaoRepresentante = new TTGOOrgaoRepresentante();
            $obTTGOOrgaoRepresentante->setDado( 'exercicio', Sessao::getExercicio() );
            $obTTGOOrgaoRepresentante->setDado( 'num_orgao', $_REQUEST['inOrgao'] );

            if ($_REQUEST['inCGMRepresentante'] != '') {
                $obTTGOOrgaoRepresentante->setDado( 'numcgm'   , $_REQUEST['inCGMRepresentante'] );
                $obTTGOOrgaoRepresentante->recuperaPorChave($rsOrgaoRepresentante);
                if ( $rsOrgaoRepresentante->getNumLinhas() > 0 ) {
                    $obTTGOOrgaoRepresentante->alteracao();
                } else {
                    $obTTGOOrgaoRepresentante->inclusao();
                }

            } else {
                $obTTGOOrgaoRepresentante->recuperaPorChave($rsOrgaoRepresentante);
                if ( $rsOrgaoRepresentante->getNumLinhas() > 0 ) {
                    $obTTGOOrgaoRepresentante->exclusao();
                }
            }

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode('É necessário cadastrar pelo um gestor!'),"n_incluir","erro");
        }
        break;
}

Sessao::encerraExcecao();
?>
