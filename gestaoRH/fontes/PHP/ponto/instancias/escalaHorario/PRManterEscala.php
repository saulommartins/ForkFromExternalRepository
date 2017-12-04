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
    * Página de Processamento para Manter Escala
    * Data de Criação: 07/10/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $

    * Casos de uso: uc-04.10.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscala.class.php"                                          );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscalaExclusao.class.php"                                  );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscalaContrato.class.php"                                  );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscalaTurno.class.php"                                     );

$stAcao      = $_REQUEST['stAcao'];
$inCodEscala = $_REQUEST['inCodEscala'];
$link        = Sessao::read('link');
$stLink      = "?".Sessao::getId()."&stAcao=$stAcao&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterEscala";
$pgFilt = "FL".$stPrograma.".php".$stLink;
$pgList = "LS".$stPrograma.".php".$stLink;
$pgForm = "FM".$stPrograma.".php".$stLink;
$pgProc = "PR".$stPrograma.".php".$stLink;
$pgOcul = "OC".$stPrograma.".php".$stLink;

$obTPontoEscala              = new TPontoEscala();
$obTPontoEscalaExclusao      = new TPontoEscalaExclusao();
$obTPontoEscalaTurno         = new TPontoEscalaTurno();

$obTPontoEscalaTurno->obTPontoEscala = &$obTPontoEscala;

$obTPontoEscalaExclusao->obTPontoEscala = &$obTPontoEscala;

switch ($stAcao) {
    case "redirecionarLista":
        $pgList = str_replace($stAcao, "alterar", $pgList);
        SistemaLegado::alertaAviso($pgList,"Escala $inCodEscala","incluir","aviso", Sessao::getId(), "../");
        break;
    case "redirecionarFiltro":
        $pgFilt = str_replace($stAcao, "alterar", $pgFilt);
        SistemaLegado::alertaAviso($pgFilt,"Escala $inCodEscala","incluir","aviso", Sessao::getId(), "../");
        break;
    case "incluir":
    case "alterar":
        Sessao::setTrataExcecao(true);

        $obTPontoEscala->setDado('descricao', $_REQUEST['stDescricao']);

        if ($stAcao == "alterar") {
            $obTPontoEscala->setDado('cod_escala', $inCodEscala);
            $obTPontoEscala->alteracao();

             $obTPontoEscalaTurno->recuperaTodos($rsTurnos, " WHERE cod_escala = ".$inCodEscala);
             while (!$rsTurnos->eof()) {
                $obTPontoEscalaTurno->setDado('cod_turno', $rsTurnos->getCampo('cod_turno'));
                $obTPontoEscalaTurno->exclusao();
                $rsTurnos->proximo();
             }

            $pgProx = $pgList;
        } else {
            $obTPontoEscala->inclusao();
            $pgProx = $pgForm;
        }

        $arTurnos = ( is_array(Sessao::read('arTurnos')) ) ? Sessao::read('arTurnos') : array();

        if (sizeof($arTurnos) > 0) {

            foreach ($arTurnos as $obTurno) {
                $obTPontoEscalaTurno->setDado('cod_turno'     ,'');
                $obTPontoEscalaTurno->setDado('dt_turno'      ,$obTurno['dtTurno']       );
                $obTPontoEscalaTurno->setDado('hora_entrada_1',$obTurno['stHoraEntrada1']);
                $obTPontoEscalaTurno->setDado('hora_saida_1'  ,$obTurno['stHoraSaida1']  );
                $obTPontoEscalaTurno->setDado('hora_entrada_2',$obTurno['stHoraEntrada2']);
                $obTPontoEscalaTurno->setDado('hora_saida_2'  ,$obTurno['stHoraSaida2']  );
                $obTPontoEscalaTurno->setDado('tipo'          ,$obTurno['stTipoDia']     );
                $obTPontoEscalaTurno->setDado('timestamp'     ,''                        );
                $obTPontoEscalaTurno->inclusao();
            }
        }

        $inCodEscala = $obTPontoEscala->getDado('cod_escala');
        $stDescricao = $obTPontoEscala->getDado('descricao');

        Sessao::encerraExcecao();
        SistemaLegado::alertaAviso($pgProx,"Escala $inCodEscala - $stDescricao","incluir","aviso", Sessao::getId(), "../");
        break;

    case "excluir":
        Sessao::setTrataExcecao(true);
        $pgProx = $pgList;

        if ($inCodEscala) {
            $obTPontoEscala->setDado('cod_escala', $inCodEscala);
            $obTPontoEscala->recuperaPorChave($rsEscala);

            if ($rsEscala->getNumLinhas() > 0) {
                $stDescricao = $rsEscala->getCampo('descricao');

                $stFiltroEscalaContratos = " AND escala_contrato.cod_escala = ".$inCodEscala;
                $obTPontoEscalaContrato = new TPontoEscalaContrato();
                $obTPontoEscalaContrato->recuperaContratosEscala($rsEscalaContratos, $stFiltroEscalaContratos);

                if ($rsEscalaContratos->getNumLinhas() > 0) {
                    Sessao::getExcecao()->setLocal('telaPrincipal');
                    Sessao::getExcecao()->setDescricao("A escala $inCodEscala - $stDescricao não pode ser excluída por possuir contratos vinculados.");
                } else {
                    $obTPontoEscalaExclusao->inclusao();
                }
            }
        }

        Sessao::encerraExcecao();
        SistemaLegado::alertaAviso($pgProx,"Escala $inCodEscala","excluir","aviso", Sessao::getId(), "../");
        break;
}

?>
