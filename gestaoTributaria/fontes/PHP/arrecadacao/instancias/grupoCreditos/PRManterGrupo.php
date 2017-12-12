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
    * Pagina de processamento para Grupos de Credito
    * Data de Criação   : 25/05/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Texeira Stephanou

    * $Id: PRManterGrupo.php 63839 2015-10-22 18:08:07Z franver $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.12  2006/10/30 13:17:04  dibueno
Adição da coluna ORDEM

Revision 1.11  2006/09/15 11:10:42  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"           );
include_once ( CAM_GT_ARR_NEGOCIO."RARRPermissao.class.php"       );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

$stCaminho = "../arrecadacao/grupoCreditos/";

//Define o nome dos arquivos PHP
$stPrograma    = "ManterGrupo";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );

function alertaAvisoCalendario($stPagina,$stMensagem,$stVariavel)
{
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
            window.open("'.$stPagina.'?stMsg='.$stMensagem.'&'.Sessao::getId().'","_blank","toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=350, height=200, top=200 , left=350");
           </script>';
}

$obAtributos = new MontaAtributos;
$obAtributos->setName('AtributoGrupo_');
$obAtributos->recuperaVetor( $arChave );

$obErro = new Erro;
$obRegra = new RARRGrupo;

$inCodAtributosSelecionados = $_REQUEST["inCodAtributoSelecionados"];

switch ($stAcao) {
    case "incluir":

    $arCreditos = Sessao::read( "creditos" );
    if ( count( $arCreditos ) > 0 ) {

        for ( $inCount=0; $inCount < count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRegra->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo );
        }

        // seta os dados vindos do formulario
        $obRegra->setDescricao( $_REQUEST["stDescricao"]    );
        $obRegra->setCodModulo( $_REQUEST["cmbModulos"]     );
        $obRegra->setExercicio( $_REQUEST["stExercicio"]    );

        // passa array de creditos
        $obRegra->setCreditos ( $arCreditos );

        foreach ($arCreditos as $arCredito) {
            $obRegra->addCredito();
            $obRegra->roUltimoCredito->setCodCredito    ($arCredito["codcredito"]   );
            $obRegra->roUltimoCredito->setCodEspecie    ($arCredito["codespecie"]   );
            $obRegra->roUltimoCredito->setCodGenero     ($arCredito["codgenero"]    );
            $obRegra->roUltimoCredito->setCodNatureza   ($arCredito["codnatureza"]  );
            $obRegra->roUltimoCredito->setDesconto      ($arCredito["desconto"]     );
            $obRegra->roUltimoCredito->setOrdem			($arCredito["ordem"]		);
/*            // verifica se credito ja foi agrupado
            $obRegra->roUltimoCredito->listarCreditos($rsCreditos);
            if ( $rsCreditos->getNumLinhas() > 0) {
                 $stChaveCredito = $arCredito["codcredito"].".".$arCredito["codespecie"].".".$arCredito["codgenero"].".".$arCredito["codnatureza"]." - ".$rsCreditos->getCampo("descricao_credito");
                 $obErro->setDescricao("Crédito ja agrupado neste exercício.($stChaveCredito)");
            }*/
        }
        // passa array de acrescimos

        foreach ( Sessao::read( 'acrescimos' ) as $arAcrescimo) {
            $obRegra->addAcrescimo();
            $obRegra->roUltimoAcrescimo->setCodAcrescimo( $arAcrescimo["codacrescimo"] );
            $obRegra->roUltimoAcrescimo->setCodTipo( $arAcrescimo["cod_tipo"] ) ;
        }

    } else {
        $obErro->setDescricao("Deve haver ao menos um crédito agrupado!");
    }

    if ($_REQUEST["inCodigoFormula"]) {
        $obRegra->setFuncaoDesoneracao( $_REQUEST["inCodigoFormula"] );
    }

    if ( !$obErro->ocorreu() ) {
        $obErro = $obRegra->agruparCreditos();
    }
    if (!$obErro->ocorreu() ) {

        SistemaLegado::alertaAviso($pgForm."?stAcao=incluir","Codigo do Grupo: ".$obRegra->getCodGrupo(),"incluir","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../");
    }
    break;

    case "alterar":

    if ( count(Sessao::read( "creditos" ) ) > 0 ) {

        for ( $inCount=0; $inCount < count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRegra->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo );
        }
        // seta os dados vindos do formulario
        $obRegra->setCodGrupo ( $_REQUEST["inCodGrupo"]    );
        $obRegra->setDescricao( $_REQUEST["stDescricao"]    );
        $obRegra->setCodModulo( $_REQUEST["cmbModulos"]     );
        $obRegra->setExercicio( $_REQUEST["stExercicio"]    );

        if ($_REQUEST["inCodigoFormula"]) {
            $obRegra->setFuncaoDesoneracao( $_REQUEST["inCodigoFormula"] );
        }

        // passa array de creditos
        foreach (Sessao::read( "creditos" ) as $arCredito) {
            $obRegra->addCredito();
            $obRegra->roUltimoCredito->setCodCredito    ($arCredito["codcredito"]   );
            $obRegra->roUltimoCredito->setCodEspecie    ($arCredito["codespecie"]   );
            $obRegra->roUltimoCredito->setCodGenero     ($arCredito["codgenero"]    );
            $obRegra->roUltimoCredito->setCodNatureza   ($arCredito["codnatureza"]  );
            $obRegra->roUltimoCredito->setDesconto      ($arCredito["desconto"]  	);
            $obRegra->roUltimoCredito->setOrdem			($arCredito["ordem"]		);
        }
        // passa array de acrescimos
        foreach ( Sessao::read( "acrescimos" ) as $arAcrescimo) {
            $obRegra->addAcrescimo();
            $obRegra->roUltimoAcrescimo->setCodAcrescimo( $arAcrescimo["codacrescimo"]);
            $obRegra->roUltimoAcrescimo->setCodTipo     ( $arAcrescimo["cod_tipo"] ) ;
        }

    } else {
        $obErro->setDescricao("Deve haver ao menos um crédito agrupado!");
    }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obRegra->alteraGrupo();
        }
        if (!$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=alterar","Codigo do Grupo: ".$obRegra->getCodGrupo(),"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../");
        }

    break;

    case "excluir":
        $obRegra->setCodGrupo( $_REQUEST["inCodGrupo"]);
        $obRegra->setExercicio( $_REQUEST["stExercicio"]);

        $obErro = $obRegra->excluirGrupo();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir","Codigo do Grupo: ".$obRegra->getCodGrupo(),"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir",urlencode($obErro->getDescricao()),"n_excluir","excluir",Sessao::getId(), "../");
        }
    break;
    case "definir":
    if ( count(Sessao::read( "grupos" ) ) > 0 ) {
        $obRARRPermissao =  new RARRPermissao;
        $obRARRPermissao->obRCGM->setNumCGM( $_REQUEST["inNumCGM"] );
        $obRARRPermissao->arGrupos = Sessao::read( "grupos" );
        // seta os dados vindos do formulario

        $obErro = $obRARRPermissao->definirPermissao();

    } else {
        $obErro->setDescricao("Deve haver ao menos um grupo definido!");
    }
    if (!$obErro->ocorreu() ) {
        SistemaLegado::alertaAviso("FLManterPermissoes.php"."?stAcao=incluir","CGM: ".$obRARRPermissao->obRCGM->getNumCGM(),"incluir","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../");
    }
    break;
}
?>
