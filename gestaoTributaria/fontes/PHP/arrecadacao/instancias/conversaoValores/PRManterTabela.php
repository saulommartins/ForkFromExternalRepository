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
    * Página de Formulario para inclusao de Tablela de Conversão
    * Data de Criacao: 11/09/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Vitor Hugo
    * @ignore

    * $Id: PRManterTabela.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.23
*/

/*
$Log$
Revision 1.1  2007/09/13 13:37:46  vitor
uc-05.03.23

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_MAPEAMENTO."TARRTabelaConversao.class.php"                                  );
include_once( CAM_GT_ARR_MAPEAMENTO."TARRTabelaConversaoValores.class.php"                           );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma    = "ManterTabela";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

switch ($stAcao) {
    case "recadastra":
        $obTARRTabelaConversaoValores = new TARRTabelaConversaoValores;
        $obTARRTabelaConversao = new TARRTabelaConversao;

        $stFiltro = " WHERE exercicio = '".$_REQUEST["inNovoExercicio"]."'";
        if ($_REQUEST["cmbTabelas"] > 0) {
            $stFiltro .= " AND cod_tabela = ".$_REQUEST["cmbTabelas"];
        }

        $obTARRTabelaConversao->recuperaListaTabelaConversao( $rsListaTabelas, $stFiltro );

        if ( !$rsListaTabelas->Eof() ) {
            sistemaLegado::alertaAviso( "FMManterRecadastro.php?".Sessao::getId()."&stAcao=recadastra","Exercício de destino já está cadastrado na base de dados.","n_erro","erro",Sessao::getId(), "../");
            exit;
        }

        $stFiltro = " WHERE exercicio = '".$_REQUEST["cmbExercicio"]."'";
        if ($_REQUEST["cmbTabelas"] > 0) {
            $stFiltro .= " AND cod_tabela = ".$_REQUEST["cmbTabelas"];
        }

        $obTARRTabelaConversao->recuperaListaTabelaConversao( $rsListaTabelas, $stFiltro );

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTARRTabelaConversao );

            while ( !$rsListaTabelas->Eof() ) {
                $obTARRTabelaConversao->setDado( "cod_tabela", $rsListaTabelas->getCampo("cod_tabela") );
                $obTARRTabelaConversao->setDado( "exercicio", $_REQUEST["inNovoExercicio"] );
                $obTARRTabelaConversao->setDado( "cod_modulo", $rsListaTabelas->getCampo("cod_modulo") );
                $obTARRTabelaConversao->setDado( "nome_tabela", $rsListaTabelas->getCampo("nome_tabela") );
                $obTARRTabelaConversao->setDado( "parametro_1", $rsListaTabelas->getCampo("parametro_1") );
                $obTARRTabelaConversao->setDado( "parametro_2", $rsListaTabelas->getCampo("parametro_2") );
                $obTARRTabelaConversao->setDado( "parametro_3", $rsListaTabelas->getCampo("parametro_3") );
                $obTARRTabelaConversao->setDado( "parametro_4", $rsListaTabelas->getCampo("parametro_4") );
                $obTARRTabelaConversao->inclusao();

                unset( $rsListaValoresTabelas );
                $stFiltro = " WHERE cod_tabela = ".$rsListaTabelas->getCampo("cod_tabela")." AND exercicio = '".$rsListaTabelas->getCampo("exercicio")."'";
                $obTARRTabelaConversaoValores->recuperaListaTabelaConversaoValores( $rsListaValoresTabelas, $stFiltro );
                while ( !$rsListaValoresTabelas->Eof() ) {
                    $obTARRTabelaConversaoValores->setDado( "cod_tabela", $rsListaTabelas->getCampo("cod_tabela") );
                    $obTARRTabelaConversaoValores->setDado( "exercicio", $_REQUEST["inNovoExercicio"] );
                    $obTARRTabelaConversaoValores->setDado( "parametro_1", $rsListaValoresTabelas->getCampo("parametro_1") );
                    $obTARRTabelaConversaoValores->setDado( "parametro_2", $rsListaValoresTabelas->getCampo("parametro_2") );
                    $obTARRTabelaConversaoValores->setDado( "parametro_3", $rsListaValoresTabelas->getCampo("parametro_3") );
                    $obTARRTabelaConversaoValores->setDado( "parametro_4", $rsListaValoresTabelas->getCampo("parametro_4") );
                    $obTARRTabelaConversaoValores->setDado( "valor", $rsListaValoresTabelas->getCampo("valor") );
                    $obTARRTabelaConversaoValores->inclusao();

                    $rsListaValoresTabelas->proximo();
                }

                $rsListaTabelas->proximo();
            }

        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso( "FMManterRecadastro.php?".Sessao::getId()."&stAcao=recadastra","Total de tabelas: ".$rsListaTabelas->getNumLinhas(),"incluir","aviso", Sessao::getId(), "../" );
        break;

    case "incluir":

     Sessao::setTrataExcecao( true );

     $obTabelaConversao = new TARRTabelaConversao();
     $obTabelaConversaoValores = new TARRTabelaConversaoValores();

     Sessao::getTransacao()->setMapeamento( $obTabelaConversao );

     $obTabelaConversao->proximoCod( $inCodTabela );

     $obTabelaConversao->setDado( "cod_tabela",  $inCodTabela              );
     $obTabelaConversao->setDado( "exercicio",   $_REQUEST["stExercicio"]  );
     $obTabelaConversao->setDado( "cod_modulo",  $_REQUEST["cmbModulos"]   );
     $obTabelaConversao->setDado( "nome_tabela", $_REQUEST["stDescricao"]  );
     $obTabelaConversao->setDado( "parametro_1", $_REQUEST["stParametro1"] );
     $obTabelaConversao->setDado( "parametro_2", $_REQUEST["stParametro2"] );
     $obTabelaConversao->setDado( "parametro_3", $_REQUEST["stParametro3"] );
     $obTabelaConversao->setDado( "parametro_4", $_REQUEST["stParametro4"] );
     $obTabelaConversao->inclusao();

     $arConvVal5 = Sessao::read( 'convval5' );
     $rsListaConversaoValores = new RecordSet;
     $rsListaConversaoValores->preenche( $arConvVal5 );

     $rsListaConversaoValores->setPrimeiroElemento();

     while (!$rsListaConversaoValores->Eof()) {
             if ($rsListaConversaoValores->getCampo("parametro_1") == "&nbsp;") { $parametro_1 = "";} else { $parametro_1 = $rsListaConversaoValores->getCampo("parametro_1"); }
             if ($rsListaConversaoValores->getCampo("parametro_2") == "&nbsp;") { $parametro_2 = "";} else { $parametro_2 = $rsListaConversaoValores->getCampo("parametro_2"); }
             if ($rsListaConversaoValores->getCampo("parametro_3") == "&nbsp;") { $parametro_3 = "";} else { $parametro_3 = $rsListaConversaoValores->getCampo("parametro_3"); }
             if ($rsListaConversaoValores->getCampo("parametro_4") == "&nbsp;") { $parametro_4 = "";} else { $parametro_4 = $rsListaConversaoValores->getCampo("parametro_4"); }

             $obTabelaConversaoValores->setDado( "cod_tabela",  $inCodTabela              );
             $obTabelaConversaoValores->setDado( "exercicio",   $rsListaConversaoValores->getCampo("stExercicio")  );
             $obTabelaConversaoValores->setDado( "cod_modulo",  $rsListaConversaoValores->getCampo("cmbModulos")   );
             $obTabelaConversaoValores->setDado( "nome_tabela", $rsListaConversaoValores->getCampo("nome_tabela")  );
             $obTabelaConversaoValores->setDado( "parametro_1", $parametro_1  );
             $obTabelaConversaoValores->setDado( "parametro_2", $parametro_2  );
             $obTabelaConversaoValores->setDado( "parametro_3", $parametro_3  );
             $obTabelaConversaoValores->setDado( "parametro_4", $parametro_4  );
             $obTabelaConversaoValores->setDado( "valor", $rsListaConversaoValores->getCampo("valor")  );
             $obTabelaConversaoValores->inclusao();

             $rsListaConversaoValores->proximo();
     }

     SistemaLegado::alertaAviso($pgForm."?stAcao=incluir","Tabela: ".$inCodTabela." incluída com sucesso.","incluir","aviso", Sessao::getId(), "../");

    Sessao::encerraExcecao();

    break;

    case "alterar":
         Sessao::setTrataExcecao( true );

         $obTabelaConversao = new TARRTabelaConversao();
         $obTabelaConversaoValores = new TARRTabelaConversaoValores();

         Sessao::getTransacao()->setMapeamento( $obTabelaConversao );

         $obTabelaConversao->setDado( "cod_tabela",  $_REQUEST["cod_tabela"]   );
         $obTabelaConversao->setDado( "exercicio",   $_REQUEST["stExercicio"]  );
         $obTabelaConversao->setDado( "cod_modulo",  $_REQUEST["cmbModulos"]   );
         $obTabelaConversao->setDado( "nome_tabela", $_REQUEST["stDescricao"]  );
         $obTabelaConversao->setDado( "parametro_1", $_REQUEST["stParametro1"] );
         $obTabelaConversao->setDado( "parametro_2", $_REQUEST["stParametro2"] );
         $obTabelaConversao->setDado( "parametro_3", $_REQUEST["stParametro3"] );
         $obTabelaConversao->setDado( "parametro_4", $_REQUEST["stParametro4"] );
         $obTabelaConversao->alteracao();

         $arConvVal4 = Sessao::read( 'convval4' );
         $rsListaConversaoValores = new RecordSet;
         $rsListaConversaoValores->preenche( $arConvVal4 );

         $rsListaConversaoValores->setPrimeiroElemento();
         $obTabelaConversaoValores->setDado( "cod_tabela",  $_REQUEST["cod_tabela"]  );
         $obTabelaConversaoValores->setDado( "exercicio",   $_REQUEST["stExercicio"]  );
         $obTabelaConversaoValores->setDado( "cod_modulo",  $_REQUEST["cmbModulos"]   );
         $obTabelaConversaoValores->setDado( "nome_tabela", $_REQUEST["stDescricao"]  );
         while (!$rsListaConversaoValores->Eof()) {
             $obTabelaConversaoValores->setDado( "parametro_1", $rsListaConversaoValores->getCampo("parametro_1")  );
             $obTabelaConversaoValores->setDado( "parametro_2", $rsListaConversaoValores->getCampo("parametro_2")  );
             $obTabelaConversaoValores->setDado( "parametro_3", $rsListaConversaoValores->getCampo("parametro_3")  );
             $obTabelaConversaoValores->setDado( "parametro_4", $rsListaConversaoValores->getCampo("parametro_4")  );
             $obTabelaConversaoValores->setDado( "valor", $rsListaConversaoValores->getCampo("valor")  );
             $obTabelaConversaoValores->exclusao();
             $rsListaConversaoValores->proximo();

         }

         $arConvVal5 = Sessao::read( 'convval5' );
         $rsListaConversaoValores = new RecordSet;
         $rsListaConversaoValores->preenche( $arConvVal5 );

         $rsListaConversaoValores->setPrimeiroElemento();

         while (!$rsListaConversaoValores->Eof()) {
                 $obTabelaConversaoValores->setDado( "cod_tabela",  $_REQUEST["cod_tabela"]  );
                 $obTabelaConversaoValores->setDado( "exercicio",   $_REQUEST["stExercicio"] );
                 $obTabelaConversaoValores->setDado( "cod_modulo",  $rsListaConversaoValores->getCampo("cmbModulos")   );
                 $obTabelaConversaoValores->setDado( "nome_tabela", $rsListaConversaoValores->getCampo("nome_tabela")  );
                 $obTabelaConversaoValores->setDado( "parametro_1", $rsListaConversaoValores->getCampo("parametro_1")  );
                 $obTabelaConversaoValores->setDado( "parametro_2", $rsListaConversaoValores->getCampo("parametro_2")  );
                 $obTabelaConversaoValores->setDado( "parametro_3", $rsListaConversaoValores->getCampo("parametro_3")  );
                 $obTabelaConversaoValores->setDado( "parametro_4", $rsListaConversaoValores->getCampo("parametro_4")  );
                 $obTabelaConversaoValores->setDado( "valor", $rsListaConversaoValores->getCampo("valor")  );
                 $obTabelaConversaoValores->inclusao();

             $rsListaConversaoValores->proximo();

         }
         SistemaLegado::alertaAviso($pgList."?stAcao=alterar","Tabela: ".$inCodTabela." alterada com sucesso.","alterar","aviso", Sessao::getId(), "../");

        Sessao::encerraExcecao();

    break;

    case "excluir":

         Sessao::setTrataExcecao( true );

         $obTabelaConversao = new TARRTabelaConversao();
         $obTabelaConversaoValores = new TARRTabelaConversaoValores();

         Sessao::getTransacao()->setMapeamento( $obTabelaConversao );

         $obTabelaConversaoValores->setDado( "cod_tabela",  $_REQUEST["inCodTabela"]   );
         $obTabelaConversaoValores->setDado( "exercicio",   $_REQUEST["stExercicio"]  );
         $obTabelaConversaoValores->exclusao();

         $obTabelaConversao->setDado( "cod_tabela",  $_REQUEST["inCodTabela"]   );
         $obTabelaConversao->setDado( "exercicio",   $_REQUEST["stExercicio"]  );
         $obTabelaConversao->setDado( "cod_modulo",  $_REQUEST["inCodModulo"]   );
         $obTabelaConversao->exclusao();

         SistemaLegado::alertaAviso($pgList."?stAcao=excluir","Tabela: ".$_REQUEST['inCodTabela'],"excluir","aviso", Sessao::getId(), "../");

        Sessao::encerraExcecao();
    break;
}
?>
