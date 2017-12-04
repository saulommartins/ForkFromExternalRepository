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
    * Arquivo de instância para manutenção de funções
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    $Id: PRManterFuncao.php 63829 2015-10-22 12:06:07Z franver $

    Casos de uso: uc-01.03.95

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GA_ADM_NEGOCIO."RFuncao.class.php");

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$stLink = "&pg=".Sessao::read('link_pg')."&pos=".Sessao::read('link_pos')."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterFuncao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new RFuncao;
$obErro  = new Erro;
$rsCorpo = new RecordSet;

$arFuncao = Sessao::read('Funcao');

switch ($stAcao) {

    case "incluir":

        $rsFuncoes = new RecordSet;
        $parametrosTipo = Sessao::read('ParametrosTipo');
        $variaveisTipo = Sessao::read('VariaveisTipo');
        $arFuncao = Sessao::read('Funcao');

        if (!$arFuncao['RetornoVar']) {
            $obErro->setDescricao("É necessário selecionar a variável de retorno");
        } else {
            include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncao.class.php");
            $obTAdministracaoFuncao = new TAdministracaoFuncao();
            $stFiltro = " WHERE proname = lower('".$_REQUEST['stNomeFuncao']."')";
            $obTAdministracaoFuncao->recuperaFuncaoCadastrada($rsFuncoes, $stFiltro);

            if ($rsFuncoes->getNumLinhas() > 0) {
                $obErro->setDescricao("Sistema já possui uma função com este nome no banco");
            } else {
                $arCorpo   = $arFuncao['Corpo'];
                $arUltimaLinha['Nivel']    = '0';
                $arUltimaLinha['Conteudo'] = 'RETORNA '.$arFuncao['RetornoVar'];
                $arCorpo[] = $arUltimaLinha;
                $rsCorpo->preenche( $arCorpo );
                list( $inCodModulo, $inCodBiblioteca ) = explode( "-", $_REQUEST['stChaveBiblioteca'] );
                $obRegra->obRBiblioteca->roRModulo->setCodModulo( $inCodModulo );
                $obRegra->obRBiblioteca->setCodigoBiblioteca( $inCodBiblioteca );
                $obRegra->setNomeFuncao                ( $_REQUEST['stNomeFuncao'] );
                $obRegra->setComentario                ( $_REQUEST['stComentario'] );
                   $obRegra->obRTipoPrimitivo->setNomeTipo( $_REQUEST['stRetorno']    );
                for ($inCount=0; $inCount<count($variaveisTipo); $inCount++) {
                    $obRegra->addVariavel();
                    $obRegra->obUltimaVariavel->setNome                      ( $variaveisTipo[$inCount]['stNomeVariavel'] );
                    $obRegra->obUltimaVariavel->obRTipoPrimitivo->setNomeTipo( $variaveisTipo[$inCount]['stTipoVariavel'] );
                    $obRegra->obUltimaVariavel->setValorInicial              ( $variaveisTipo[$inCount]['stValorVariavel'] );
                    $obRegra->commitVariavel();
                }
                for ($inCount=0; $inCount<count($parametrosTipo); $inCount++) {
                    $obRegra->addParametro();
                    $obRegra->obUltimaVariavel->setNome                      ( $parametrosTipo[$inCount]['stNomeParametro'] );
                    $obRegra->obUltimaVariavel->obRTipoPrimitivo->setNomeTipo( $parametrosTipo[$inCount]['stTipoParametro'] );
                    $obRegra->obUltimaVariavel->setOrdem                     ( $inCount );
                    $obRegra->commitVariavel();
                }
                $obRegra->montaCorpoFuncao();
                $obRegra->ln2pl();
                $obRegra->setRSCorpoLN( $rsCorpo );

                $obErro = $obRegra->salvar();
            }
        }

        Sessao::write('Funcao',$arFuncao);

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Função ".$_REQUEST['stNomeFuncao'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
        break;

    case "alterar":

        $parametrosTipo = Sessao::read('ParametrosTipo');
        $variaveisTipo = Sessao::read('VariaveisTipo');
        $arFuncao = Sessao::read('Funcao');

        if (!$arFuncao['RetornoVar']) {
            $obErro->setDescricao("É necessário selecionar a variavel de retorno");
        }
        if ( !$obErro->ocorreu() ) {
            $arCorpo   = $arFuncao['Corpo'];
            $arUltimaLinha['Nivel']    = '0';
            $arUltimaLinha['Conteudo'] = 'RETORNA '.$arFuncao['RetornoVar'];
            $arCorpo[] = $arUltimaLinha;

            for ($inCount=0; $inCount < count($arCorpo);$inCount++ ) {
                $stTemp = $arCorpo[$inCount]['Conteudo'];
                $stTemp = str_replace("\'\'", '"', $stTemp);
                $arCorpo[$inCount]['Conteudo'] = $stTemp;
            }

            $rsCorpo->preenche( $arCorpo );

            list( $inCodModulo, $inCodBiblioteca ) = explode( "-", $_REQUEST['stChaveBiblioteca'] );
            $obRegra->obRBiblioteca->roRModulo->setCodModulo( $inCodModulo );
            $obRegra->obRBiblioteca->setCodigoBiblioteca( $inCodBiblioteca );
            $obRegra->setCodFuncao                 ( $_REQUEST['inCodFuncao'] );
            $obRegra->setNomeFuncao                ( $arFuncao['Nome'] );
            $obRegra->setComentario                ( $_REQUEST['stComentario'] );
            $obRegra->obRTipoPrimitivo->setNomeTipo( $arFuncao['Retorno'] );
            for ($inCount=0; $inCount<count($variaveisTipo); $inCount++) {
                $obRegra->addVariavel();
                $obRegra->obUltimaVariavel->setNome                      ( $variaveisTipo[$inCount]['stNomeVariavel'] );
                $obRegra->obUltimaVariavel->obRTipoPrimitivo->setNomeTipo( $variaveisTipo[$inCount]['stTipoVariavel'] );
                $obRegra->obUltimaVariavel->setValorInicial              ( $variaveisTipo[$inCount]['stValorVariavel'] );
                $obRegra->commitVariavel();
            }
            for ($inCount=0; $inCount<count($parametrosTipo); $inCount++) {
                $obRegra->addParametro();
                $obRegra->obUltimaVariavel->setNome                      ( $parametrosTipo[$inCount]['stNomeParametro'] );
                $obRegra->obUltimaVariavel->obRTipoPrimitivo->setNomeTipo( $parametrosTipo[$inCount]['stTipoParametro'] );
                $obRegra->obUltimaVariavel->setOrdem                     ( $inCount );
                $obRegra->commitVariavel();
            }

            $obRegra->montaCorpoFuncao();

//          $stCorpoLN = str_replace("\\'\\'",'"',$stCorpoLN);
  //        $stCorpoLN = str_replace('\'\'','"',$stCorpoLN);
    //      $stCorpoLN = str_replace("\\\'","\'",$stCorpoLN);
      ///   $stCorpoLN = str_replace('\"','"',$stCorpoLN);
         // $stCorpoLN = str_replace('\'','"',$stCorpoLN);
//          $stCorpoLN = str_replace("'", '"', $stCorpoLN);
  //        $stCorpoLN = str_replace('\"\"','"',$stCorpoLN);

            $obRegra->ln2pl();
            $obRegra->setRSCorpoLN( $rsCorpo );

            $obErro = $obRegra->salvar();

        }

        Sessao::write('Funcao',$arFuncao);

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."&pos=".$_REQUEST['pos']."&pg=".$_REQUEST['pg'],"Função: ".$arFuncao['Nome'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode( "ERRO: Para alterar, o usuário deve ser o dono da função ".$arFuncao['Nome'] ),"n_alterar","erro");
        }
        break;

    case "excluir":
        $obRegra->obRBiblioteca->roRModulo->setCodModulo( $_REQUEST['inCodModulo'] );
        $obRegra->obRBiblioteca->setCodigoBiblioteca( $_REQUEST['inCodBiblioteca'] );
        $obRegra->setCodFuncao                 ( $_REQUEST['inCodFuncao'] );
        $obRegra->consultar();
        $obErro = $obRegra->excluir();

        Sessao::write('Funcao',$arFuncao);

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."&pos=".$_REQUEST['pos']."&pg=".$_REQUEST['pg'],"Função: ".$obRegra->getNomeFuncao(),"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode("ERRO: Para excluir, o usuário deve ser o dono da função ".$obRegra->getNomeFuncao()),"n_excluir","erro", Sessao::getId(), "../");
        }
        break;
    case "salva_fica":

        $parametrosTipo = Sessao::read('ParametrosTipo');
        $variaveisTipo = Sessao::read('VariaveisTipo');

        if (!$arFuncao['RetornoVar']) {
            $obErro->setDescricao("É necessário selecionar a variavel de retorno");
        }
        if ( !$obErro->ocorreu() ) {
            $arCorpo   = $arFuncao['Corpo'];
            $arUltimaLinha['Nivel']    = '0';
            $arUltimaLinha['Conteudo'] = 'RETORNA '.$arFuncao['RetornoVar'];
            $arCorpo[] = $arUltimaLinha;

            for ($inCount=0; $inCount < count($arCorpo);$inCount++ ) {
                $stTemp = $arCorpo[$inCount]['Conteudo'];
                $stTemp = str_replace("\'\'", '"', $stTemp);
                $arCorpo[$inCount]['Conteudo'] = $stTemp;
            }

            $rsCorpo->preenche( $arCorpo );

            list( $inCodModulo, $inCodBiblioteca ) = explode( "-", $_REQUEST['stChaveBiblioteca'] );
            $obRegra->obRBiblioteca->roRModulo->setCodModulo( $inCodModulo );
            $obRegra->obRBiblioteca->setCodigoBiblioteca( $inCodBiblioteca );
            $obRegra->setCodFuncao                 ( $_REQUEST['inCodFuncao'] );
            $obRegra->setNomeFuncao                ( $arFuncao['Nome'] );
            $obRegra->setComentario                ( $_REQUEST['stComentario'] );
            $obRegra->obRTipoPrimitivo->setNomeTipo( $arFuncao['Retorno'] );
            for ($inCount=0; $inCount<count($variaveisTipo); $inCount++) {
                $obRegra->addVariavel();
                $obRegra->obUltimaVariavel->setNome                      ( $variaveisTipo[$inCount]['stNomeVariavel'] );
                $obRegra->obUltimaVariavel->obRTipoPrimitivo->setNomeTipo( $variaveisTipo[$inCount]['stTipoVariavel'] );
                $obRegra->obUltimaVariavel->setValorInicial              ( $variaveisTipo[$inCount]['stValorVariavel'] );
                $obRegra->commitVariavel();
            }
            for ($inCount=0; $inCount<count($parametrosTipo); $inCount++) {
                $obRegra->addParametro();
                $obRegra->obUltimaVariavel->setNome                      ( $parametrosTipo[$inCount]['stNomeParametro'] );
                $obRegra->obUltimaVariavel->obRTipoPrimitivo->setNomeTipo( $parametrosTipo[$inCount]['stTipoParametro'] );
                $obRegra->obUltimaVariavel->setOrdem                     ( $inCount );
                $obRegra->commitVariavel();
            }

            $obRegra->montaCorpoFuncao();
            $obRegra->ln2pl();
            $obRegra->setRSCorpoLN( $rsCorpo );

            $obErro = $obRegra->salvar();

            Sessao::write('Funcao',$arFuncao);

            if ( !$obErro->ocorreu() ) {
                SistemaLegado::exibeAviso($arFuncao['Nome'],"alterar","alterar");
            } else {
                SistemaLegado::exibeAviso(urlencode( "ERRO: Para alterar, o usuário deve ser o dono da função ".$arFuncao['Nome'] ),"n_alterar","erro");
            }
        }
        break;
}
?>
