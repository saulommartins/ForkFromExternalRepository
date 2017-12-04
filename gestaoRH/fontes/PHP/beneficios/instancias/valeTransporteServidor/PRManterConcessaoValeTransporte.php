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
    * Processamento de Vale-Tranporte Servidor
    * Data de Cria??o: 13/10/2005

    * @author Analista: Vandr? Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30880 $
    $Name$
    $Author: souzadl $
    $Date: 2006-09-26 07:00:14 -0300 (Ter, 26 Set 2006) $

    * Casos de uso: uc-04.06.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioContratoServidorConcessaoValeTransporte.class.php"        );
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$arSessaoLink = Sessao::read('link');
$stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterConcessaoValeTransporte";
$pgFilt    = "FL".$stPrograma.".php?".Sessao::getId().$stLink;
$pgList    = "LS".$stPrograma.".php?".Sessao::getId().$stLink;
$pgForm    = "FM".$stPrograma.".php?".Sessao::getId().$stLink;
$pgProc    = "PR".$stPrograma.".php?".Sessao::getId().$stLink;
$pgOcul    = "OC".$stPrograma.".php?".Sessao::getId().$stLink;

$obRBeneficioContratoServidorConcessaoValeTransporte  = new RBeneficioContratoServidorConcessaoValeTransporte;
$obCalendario = new Calendario;
$obErro = new erro;
switch ($stAcao) {
    case "incluir":
        $obRBeneficioContratoServidorConcessaoValeTransporte->setTipoConcessao($_POST['stConcessao']);

        if ( Sessao::read('boUtilizarGrupo') ) {
            $obRBeneficioContratoServidorConcessaoValeTransporte->setUtilizarGrupo( true );
            foreach (Sessao::read('concessoes') as $arConcessao) {
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setCodGrupo                                         ( $_POST['inCodGrupo']           );
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodConcessao( $arConcessao['inCodConcessao'] );
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio   ( $arConcessao['inAno']          );
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes      ( $arConcessao['inCodMes']       );
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->setRegistro                                         ( $_POST['inContrato']           );
            }
            $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->incluirContratoServidorConcessaoValeTransporte();
            if ( !$obErro->ocorreu() ) {
                $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->listarGrupoConcessao($rsGrupo);
                $stMensagem = "Grupo: ".$rsGrupo->getCampo('descricao') ;
            }
        } else {
            if ( (!Sessao::read('concessoes')) or (count ( Sessao::read('concessoes')) == 0 )) {
                  $obErro->setDescricao( 'Inclua ao menos uma concessão antes de confirmar a operação.');
            } else {
                foreach (Sessao::read('concessoes') as $arConcessao) {
                    if ($_POST['stConcessao'] == 'contrato' or $_POST['stConcessao'] == 'cgm_contrato') {
                        $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
                        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes                                        ( $arConcessao['inCodMes']            );
                        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio                                     ( $arConcessao['inAno']               );
                        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRBeneficioValeTransporte->setCodValeTransporte ( $arConcessao['inCodValeTransporte'] );
                        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodTipo                                       ( $arConcessao['inCodTipo']           );
                        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->setCodCalendar                    ( $arConcessao['inCodCalendario']     );
                        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setQuantidade                                    ( $arConcessao['inQuantidadeMensal']  );
                        $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setVigencia                                      ( $arConcessao['dtVigencia']          );
                        if ($arConcessao['arQuantidadeSemanal']) {
                            $arQuantidadeSemanal = $arConcessao['arQuantidadeSemanal'];
                            for ($inIndex=0;$inIndex<7;$inIndex++) {
                                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->addRBeneficioConcessaoValeTransporteSemanal();
                                $inQuantidade  = ( $arQuantidadeSemanal[$inIndex]['inQuantidade'] != "" ) ? $arQuantidadeSemanal[$inIndex]['inQuantidade'] : 0;
                                $boObrigatorio = ( $arQuantidadeSemanal[$inIndex]['boObrigatorio']== "on" ) ? "true" : "false";
                                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->setQuantidade ( $inQuantidade    );
                                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->setObrigatorio( $boObrigatorio   );
                                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->setCodDia     ( $inIndex+1       );
                            }
                            if ( count($arConcessao['arQuantidadeMensal']) <= 0 ) {
                                $inDiasMes = $obCalendario->retornaUltimoDiaMes($arConcessao['inCodMes'],$arConcessao['inAno']);
                                for ($inDia=1;$inDia<=$inDiasMes;$inDia++) {
                                    $inDiaSemana   = $obCalendario->retornaDiaSemana($inDia,$arConcessao['inCodMes'],$arConcessao['inAno']);
                                    $inDia         = ( $inDia < 10 ) ? '0'.$inDia : $inDia;
                                    $inMes         = ( $arConcessao['inCodMes'] < 10 ) ? '0'.$arConcessao['inCodMes'] : $arConcessao['inCodMes'];
                                    $inQuantidade  = ( $arQuantidadeSemanal[$inDiaSemana]['inQuantidade'] == "" ) ? 0 : $arQuantidadeSemanal[$inDiaSemana]['inQuantidade'];
                                    $boObrigatorio = ( $arQuantidadeSemanal[$inDiaSemana]['boObrigatorio']== "on" ) ? "true" : "false";
                                    $dtDia         = $inDia."/".$inMes."/".$arConcessao['inAno'];
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->addRBeneficioConcessaoValeTransporteDiario();
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setDia( $dtDia );
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setQuantidade( $inQuantidade );
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setObrigatorio( $boObrigatorio);
                                }
                            } else {
                                foreach ($arConcessao['arQuantidadeMensal'] as $arQuantidadeMensal) {
                                    $inQuantidade  = ( $arQuantidadeMensal['inQuantidade'] == "" ) ? 0 : $arQuantidadeMensal['inQuantidade'];
                                    $boObrigatorio = ( $arQuantidadeMensal['boObrigatorio'] == "on" ) ? "true" : "false";
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->addRBeneficioConcessaoValeTransporteDiario();
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setDia( $arQuantidadeMensal['stData'] );
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setQuantidade( $inQuantidade );
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setObrigatorio( $boObrigatorio );
                                }
                            }
                        }
                    } else {
                        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
                        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes                                        ( $arConcessao['inCodMes']            );
                        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio                                     ( $arConcessao['inAno']               );
                        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->obRBeneficioValeTransporte->setCodValeTransporte ( $arConcessao['inCodValeTransporte'] );
                        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodTipo                                       ( $arConcessao['inCodTipo']           );
                        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->obRCalendario->setCodCalendar                    ( $arConcessao['inCodCalendario']     );
                        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setQuantidade                                    ( $arConcessao['inQuantidadeMensal']  );
                        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setVigencia                                      ( $arConcessao['dtVigencia']          );
                        if ($arConcessao['arQuantidadeSemanal']) {
                            $arQuantidadeSemanal = $arConcessao['arQuantidadeSemanal'];
                            for ($inIndex=0;$inIndex<7;$inIndex++) {
                                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->addRBeneficioConcessaoValeTransporteSemanal();
                                $inQuantidade  = ( $arQuantidadeSemanal[$inIndex]['inQuantidade'] != "" ) ? $arQuantidadeSemanal[$inIndex]['inQuantidade'] : 0;
                                $boObrigatorio = ( $arQuantidadeSemanal[$inIndex]['boObrigatorio']== "on" ) ? "true" : "false";
                                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->setQuantidade ( $inQuantidade    );
                                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->setObrigatorio( $boObrigatorio   );
                                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->setCodDia     ( $inIndex+1       );
                            }
                            if ( count($arConcessao['arQuantidadeMensal']) <= 0 ) {
                                $inDiasMes = $obCalendario->retornaUltimoDiaMes($arConcessao['inCodMes'],$arConcessao['inAno']);
                                for ($inDia=1;$inDia<=$inDiasMes;$inDia++) {
                                    $inDiaSemana   = $obCalendario->retornaDiaSemana($inDia,$arConcessao['inCodMes'],$arConcessao['inAno']);
                                    $inDia         = ( $inDia < 10 ) ? '0'.$inDia : $inDia;
                                    $inMes         = ( $arConcessao['inCodMes'] < 10 ) ? '0'.$arConcessao['inCodMes'] : $arConcessao['inCodMes'];
                                    $inQuantidade  = ( $arQuantidadeSemanal[$inDiaSemana]['inQuantidade'] == "" ) ? 0 : $arQuantidadeSemanal[$inDiaSemana]['inQuantidade'];
                                    $boObrigatorio = ( $arQuantidadeSemanal[$inDiaSemana]['boObrigatorio']== "on" ) ? "true" : "false";
                                    $dtDia         = $inDia."/".$inMes."/".$arConcessao['inAno'];
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->addRBeneficioConcessaoValeTransporteDiario();
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setDia( $dtDia );
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setQuantidade( $inQuantidade );
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setObrigatorio( $boObrigatorio);
                                }
                            } else {
                                foreach ($arConcessao['arQuantidadeMensal'] as $arQuantidadeMensal) {
                                    $inQuantidade  = ( $arQuantidadeMensal['inQuantidade'] == "" ) ? 0 : $arQuantidadeMensal['inQuantidade'];
                                    $boObrigatorio = ( $arQuantidadeMensal['boObrigatorio'] == "on" ) ? "true" : "false";
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->addRBeneficioConcessaoValeTransporteDiario();
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setDia( $arQuantidadeMensal['stData'] );
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setQuantidade( $inQuantidade );
                                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setObrigatorio( $boObrigatorio );
                                }
                            }
                        }

                    }
                }

                if ($_POST['stConcessao'] == 'contrato' or $_POST['stConcessao'] == 'cgm_contrato') {
                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->setRegistro( $_POST['inContrato'] );
                    $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->incluirContratoServidorConcessaoValeTransporte();
                    $stMensagem = "Matrícula: ". $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->getRegistro();
                } else {
                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setDescricao( $_POST['stDescricaoGrupo'] );
                    //$obRBeneficioContratoServidorConcessaoValeTransporte->setUtilizarGrupo(true);
                    $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->incluirGrupoConcessao();
                    $stMensagem = "Grupo: ". $_POST['stDescricaoGrupo'];
                }
                //$obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->incluirContratoServidorConcessaoValeTransporte();
                }
            }

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $obRBeneficioContratoServidorConcessaoValeTransporte->setTipoConcessao($_POST['stConcessao']);
        if ( Sessao::read('boUtilizarGrupo') ) {
            $obRBeneficioContratoServidorConcessaoValeTransporte->setUtilizarGrupo( true );
            foreach (Sessao::read('concessoes') as $arConcessao) {
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setCodGrupo                                         ( $_POST['inCodGrupo']           );
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodConcessao( $arConcessao['inCodConcessao'] );
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio   ( $arConcessao['inAno']          );
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes      ( $arConcessao['inCodMes']       );
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->setRegistro                                         ( $_POST['inRegistro']           );
            }
            $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->alterarContratoServidorConcessaoValeTransporte();
            if ( !$obErro->ocorreu() ) {
                $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->listarGrupoConcessao($rsGrupo);
                $stMensagem = "Grupo: ".$rsGrupo->getCampo('descricao') ;
            }
        } else {

            foreach (Sessao::read('concessoes') as $arConcessao) {
                if ($_POST['stConcessao'] == 'contrato' or $_POST['stConcessao'] == 'cgm_contrato') {

                    $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes                                        ( $arConcessao['inCodMes']            );
                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio                                     ( $arConcessao['inAno']               );
                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRBeneficioValeTransporte->setCodValeTransporte ( $arConcessao['inCodValeTransporte'] );
                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodTipo                                       ( $arConcessao['inCodTipo']           );
                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->setCodCalendar                    ( $arConcessao['inCodCalendario']     );
                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setQuantidade                                    ( $arConcessao['inQuantidadeMensal']  );
                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodConcessao                                  ( $arConcessao['inCodConcessao']      );
                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setVigencia                                      ( $arConcessao['dtVigencia']          );

                    if ($arConcessao['arQuantidadeSemanal']) {

                        $arQuantidadeSemanal = $arConcessao['arQuantidadeSemanal'];
                        for ($inIndex=0;$inIndex<7;$inIndex++) {
                            $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->addRBeneficioConcessaoValeTransporteSemanal();
                            $inQuantidade  = ( $arQuantidadeSemanal[$inIndex]['inQuantidade'] != "" ) ? $arQuantidadeSemanal[$inIndex]['inQuantidade'] : 0;
                            $boObrigatorio = ( $arQuantidadeSemanal[$inIndex]['boObrigatorio']== "on" ) ? "true" : "false";
                            $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->setQuantidade ( $inQuantidade    );
                            $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->setObrigatorio( $boObrigatorio   );
                            $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->setCodDia     ( $inIndex+1       );
                        }
                        if ( count($arConcessao['arQuantidadeMensal']) <= 0 ) {
                            $inDiasMes = $obCalendario->retornaUltimoDiaMes($arConcessao['inCodMes'],$arConcessao['inAno']);
                            for ($inDia=1;$inDia<=$inDiasMes;$inDia++) {
                                $inDiaSemana   = $obCalendario->retornaDiaSemana($inDia,$arConcessao['inCodMes'],$arConcessao['inAno']);
                                $inDia         = ( $inDia < 10 ) ? '0'.$inDia : $inDia;
                                $inMes         = ( $arConcessao['inCodMes'] < 10 ) ? '0'.$arConcessao['inCodMes'] : $arConcessao['inCodMes'];
                                $inQuantidade  = ( $arQuantidadeSemanal[$inDiaSemana]['inQuantidade'] == "" ) ? 0 : $arQuantidadeSemanal[$inDiaSemana]['inQuantidade'];
                                $boObrigatorio = ( $arQuantidadeSemanal[$inDiaSemana]['boObrigatorio']== "on" ) ? "true" : "false";
                                $dtDia         = $inDia."/".$inMes."/".$arConcessao['inAno'];
                                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->addRBeneficioConcessaoValeTransporteDiario();
                                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setDia( $dtDia );
                                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setQuantidade( $inQuantidade );
                                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setObrigatorio( $boObrigatorio);
                            }
                        } else {
                            foreach ($arConcessao['arQuantidadeMensal'] as $arQuantidadeMensal) {
                                $inQuantidade  = ( $arQuantidadeMensal['inQuantidade'] == "" ) ? 0 : $arQuantidadeMensal['inQuantidade'];
                                $boObrigatorio = ( $arQuantidadeMensal['boObrigatorio'] == "on" ) ? "true" : "false";
                                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->addRBeneficioConcessaoValeTransporteDiario();
                                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setDia( $arQuantidadeMensal['stData'] );
                                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setQuantidade( $inQuantidade );
                                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setObrigatorio( $boObrigatorio );
                            }
                        }
                    }
                } else {

                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes                                        ( $arConcessao['inCodMes']            );
                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio                                     ( $arConcessao['inAno']               );
                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->obRBeneficioValeTransporte->setCodValeTransporte ( $arConcessao['inCodValeTransporte'] );
                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodTipo                                       ( $arConcessao['inCodTipo']           );
                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->obRCalendario->setCodCalendar                    ( $arConcessao['inCodCalendario']     );
                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setQuantidade                                    ( $arConcessao['inQuantidadeMensal']  );
                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setVigencia                                      ( $arConcessao['dtVigencia']          );
                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setCodGrupo                                                                           ( Sessao::read('inCodGrupo')          );
                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodConcessao                                  ( $arConcessao['inCodConcessao']      );
                    if ($arConcessao['arQuantidadeSemanal']) {
                        $arQuantidadeSemanal = $arConcessao['arQuantidadeSemanal'];
                        for ($inIndex=0;$inIndex<7;$inIndex++) {
                            $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->addRBeneficioConcessaoValeTransporteSemanal();
                            $inQuantidade  = ( $arQuantidadeSemanal[$inIndex]['inQuantidade'] != "" ) ? $arQuantidadeSemanal[$inIndex]['inQuantidade'] : 0;
                            $boObrigatorio = ( $arQuantidadeSemanal[$inIndex]['boObrigatorio']== "on" ) ? "true" : "false";
                            $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->setQuantidade ( $inQuantidade    );
                            $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->setObrigatorio( $boObrigatorio   );
                            $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->setCodDia     ( $inIndex+1       );
                        }
                        if ( count($arConcessao['arQuantidadeMensal']) <= 0 ) {
                            $inDiasMes = $obCalendario->retornaUltimoDiaMes($arConcessao['inCodMes'],$arConcessao['inAno']);
                            for ($inDia=1;$inDia<=$inDiasMes;$inDia++) {
                                $inDiaSemana   = $obCalendario->retornaDiaSemana($inDia,$arConcessao['inCodMes'],$arConcessao['inAno']);
                                $inDia         = ( $inDia < 10 ) ? '0'.$inDia : $inDia;
                                $inMes         = ( $arConcessao['inCodMes'] < 10 ) ? '0'.$arConcessao['inCodMes'] : $arConcessao['inCodMes'];
                                $inQuantidade  = ( $arQuantidadeSemanal[$inDiaSemana]['inQuantidade'] == "" ) ? 0 : $arQuantidadeSemanal[$inDiaSemana]['inQuantidade'];
                                $boObrigatorio = ( $arQuantidadeSemanal[$inDiaSemana]['boObrigatorio']== "on" ) ? "true" : "false";
                                $dtDia         = $inDia."/".$inMes."/".$arConcessao['inAno'];
                                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->addRBeneficioConcessaoValeTransporteDiario();
                                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setDia( $dtDia );
                                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setQuantidade( $inQuantidade );
                                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setObrigatorio( $boObrigatorio);
                            }
                        } else {
                            foreach ($arConcessao['arQuantidadeMensal'] as $arQuantidadeMensal) {
                                $inQuantidade  = ( $arQuantidadeMensal['inQuantidade'] == "" ) ? 0 : $arQuantidadeMensal['inQuantidade'];
                                $boObrigatorio = ( $arQuantidadeMensal['boObrigatorio'] == "on" ) ? "true" : "false";
                                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->addRBeneficioConcessaoValeTransporteDiario();
                                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setDia( $arQuantidadeMensal['stData'] );
                                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setQuantidade( $inQuantidade );
                                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->roRBeneficioConcessaoValeTransporteSemanal->roRBeneficioConcessaoValeTransporteDiario->setObrigatorio( $boObrigatorio );
                            }
                        }
                    }

                }
            }
            $stConcessao = $_POST['stConcessao'];
            if ($stConcessao == 'contrato' or $stConcessao == 'cgm_contrato') {
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->setCodContrato( $_POST['inRegistro'] );
                $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->alterarContratoServidorConcessaoValeTransporte();
                $stMensagem = "Matrícula: ". $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->getRegistro();
            } else {
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setDescricao( $_POST['stDescricaoGrupo'] );
                $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->alterarGrupoConcessao();
                $stMensagem = "Grupo: ". $_POST['stDescricaoGrupo'];
            }
        }
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir":
        if ( ($arSessaoLink['stConcessao'] == 'contrato' or $arSessaoLink['stConcessao'] == 'cgm_contrato') and $_GET['inCodGrupo'] == 0 ) {
            $inCodConcessao = $_GET['inCodConcessao'];
            $inCodMes       = $_GET['inCodMes'];
            $inExercicio    = $_GET['inExercicio'];
            $boAgrupar      = $_GET['boAgrupar'];
            $obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
            if ($boAgrupar) {
                $arCodConcessao = explode("-",$inCodConcessao);
                foreach ($arCodConcessao as $inCodConcessao) {
                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodConcessao($inCodConcessao);
                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes($inCodMes);
                    $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio($inExercicio);
                    $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->excluirContratoServidorConcessaoValeTransporte();
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            } else {
                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodConcessao($inCodConcessao);
                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes($inCodMes);
                $obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio($inExercicio);
                $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->excluirContratoServidorConcessaoValeTransporte();
            }
            $stMensagem = "Contranto/Mês: ".$_GET['stDescQuestao'];
        } else {
            $inCodContrato  = $_GET['inCodContrato'];
            $inCodGrupo     = $_GET['inCodGrupo'];
            $inCodConcessao = $_GET['inCodConcessao'];
            $inCodMes       = $_GET['inCodMes'];
            $inExercicio    = $_GET['inExercicio'];
            $boAgrupar      = $_GET['boAgrupar'];
            if ($arSessaoLink['stConcessao'] == 'grupo') {
                $obRBeneficioContratoServidorConcessaoValeTransporte->setUtilizarGrupo(true);
                $stMensagem = "Grupo/Mês: ".$_GET['stDescQuestao'];
            } else {
                $stMensagem = "Matrícula/Grupo: ".$_GET['stDescQuestao'];
            }
            if ($boAgrupar) {
                $arCodConcessao = explode("-",$inCodConcessao);
                foreach ($arCodConcessao as $inCodConcessao) {
                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setCodGrupo($inCodGrupo);
                    $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->setCodContrato($inCodContrato);
                    $obRBeneficioContratoServidorConcessaoValeTransporte->listarContratoServidorGrupoConcessaoValeTransporte($rsConcessoesGrupo);
                    $obErro = new erro;
                    if ( $rsConcessoesGrupo->getNumLinhas() > 0 ) {
                        $obErro->setDescricao("O grupo está sendo utilizado por um contrato.");
                    }
                    if ( !$obErro->ocorreu() ) {
                        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodConcessao($inCodConcessao);
                        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes($inCodMes);
                        $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio($inExercicio);
                        $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->excluirContratoServidorConcessaoValeTransporte();
                    }
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            } else {
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setCodGrupo($inCodGrupo);
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->setCodContrato($inCodContrato);
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodConcessao($inCodConcessao);
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes($inCodMes);
                $obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio($inExercicio);
                $obErro = $obRBeneficioContratoServidorConcessaoValeTransporte->excluirContratoServidorConcessaoValeTransporte();
            }
        }
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,$stMensagem,"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,urlencode( $obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;
}
?>
