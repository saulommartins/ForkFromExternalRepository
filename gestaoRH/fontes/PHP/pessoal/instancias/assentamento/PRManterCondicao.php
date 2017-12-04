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
* Processamento da condição do assentamento
* Data de Criação: 05/08/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage

$Revision: 30860 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.04.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamentoVinculado.class.php"      );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCondicaoAssentamento.class.php"       );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamento.class.php"               );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalVantagem.class.php"                   );

$arLink = Sessao::read('link');
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterCondicao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink."&inCodClassificacaoTxt=".$_POST['inCodClassificacaoTxt'];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRPessoalVantagem               = new RPessoalVantagem;
$obRPessoalAssentamento1          = new RPessoalAssentamento($obRPessoalVantagem);
$obRPessoalAssentamento2          = new RPessoalAssentamento($obRPessoalVantagem);
$obRPessoalCondicaoAssentamento   = new RPessoalCondicaoAssentamento();
$obRPessoalAssentamentoVinculado  = new RPessoalAssentamentoVinculado( $obRPessoalAssentamento1,$obRPessoalAssentamento2,$obRPessoalCondicaoAssentamento );

switch ($stAcao) {
    case "incluir":
        if ( count( Sessao::read('assentamentoVinculado')) == 0 ) {
                 sistemaLegado::exibeAviso('Selecione pelo menos uma condição de assentamento para vinculação' ,"n_incluir","erro");
        } else {

            foreach ( Sessao::read('assentamentoVinculado') as $key=>$arAssentamentoVinculado ) {
                $obRPessoalCondicaoAssentamento->addAssentamentoVinculado();

                $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->
                                                                                            setSigla( $_POST['stSigla'] );

                $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->
                                                                                     listarAssentamento( $rsAssentamento );

                $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->
                                                       setCodAssentamento( $rsAssentamento->getCampo('cod_assentamento') );

                $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->
                                                                    setTimestamp( $rsAssentamento->getCampo('timestamp') );

                $rsAssentamento = new RecordSet;

                $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento2->setSigla( $arAssentamentoVinculado['stSiglaVinculado'] );
                $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento2->listarAssentamento( $rsAssentamento );
                $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento2->setCodAssentamento( $rsAssentamento->getCampo('cod_assentamento') );
                $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento2->setTimestamp( $rsAssentamento->getCampo('timestamp') );
                $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->setCondicao           ( $arAssentamentoVinculado['boCondicao']          );
                $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->setDiasProtelarAverbar( $arAssentamentoVinculado['inDiasVinculado']     );
                $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->setDiasIncidencia     ( $arAssentamentoVinculado['inDiasIncidencia']     );

                $arCodFuncao = explode('.',$arAssentamentoVinculado["inCodFuncaoTxt"]);

                if (count( $arCodFuncao ) == 3 ) {
                    $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRFuncao->setCodFuncao    ( $arCodFuncao[2] );
                    $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
                    $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
                 }
            }
            $obErro = $obRPessoalCondicaoAssentamento->incluirCondicaoAssentamento();
            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgForm,"Definir Condições Averbação/Protelação","incluir","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        }
    break;

    case "alterar":

        if ( count(Sessao::read('assentamentoVinculado')) == 0 ) {

            $obRPessoalCondicaoAssentamento->addAssentamentoVinculado();
            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->setSigla( $_POST['stSigla'] );
            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->listarAssentamento( $rsAssentamento );
            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->setCodAssentamento( $rsAssentamento->getCampo('cod_assentamento') );
            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->setTimestamp( $rsAssentamento->getCampo('timestamp') );
            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->setSigla( "filho_nulo" );
        }

        foreach ( Sessao::read('assentamentoVinculado') as $key=>$arAssentamentoVinculado ) {

            $obRPessoalCondicaoAssentamento->addAssentamentoVinculado();
            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->setSigla( $_POST['stSigla'] );
            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->listarAssentamento( $rsAssentamento );
            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->setCodAssentamento( $rsAssentamento->getCampo('cod_assentamento') );
            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->setTimestamp( $rsAssentamento->getCampo('timestamp') );

            $rsAssentamento = new RecordSet;

            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento2->setSigla( $arAssentamentoVinculado['stSiglaVinculado'] );
            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento2->listarAssentamento( $rsAssentamento );
            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento2->setCodAssentamento( $rsAssentamento->getCampo('cod_assentamento') );
            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento2->setTimestamp( $rsAssentamento->getCampo('timestamp') );

            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->setCondicao             ( $arAssentamentoVinculado['boCondicao']       );
            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->setDiasProtelarAverbar  ( $arAssentamentoVinculado['inDiasVinculado']  );
            $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->setDiasIncidencia       ( $arAssentamentoVinculado['inDiasIncidencia'] );

            $arCodFuncao = explode('.',$arAssentamentoVinculado["inCodFuncaoTxt"]);

            if (count( $arCodFuncao ) == 3 ) {
                $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRFuncao->setCodFuncao( $arCodFuncao[2] );
                $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
                $obRPessoalCondicaoAssentamento->roUltimoRPessoalAssentamentoVinculado->obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
            }
        }

        $obRPessoalCondicaoAssentamento->setCodCondicao($_POST['inCodCondicao']);
        $obErro = $obRPessoalCondicaoAssentamento->alterarCondicaoAssentamento();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Definir Condições Averbação/Protelação","alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

}
?>
