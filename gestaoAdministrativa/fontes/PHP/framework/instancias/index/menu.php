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
 * Arquivo responsável por montar os menus do sistema
 * Data de Criação: 00/00/2005

 * @author Analista: Cassiano de Vasconcellos Ferreira
 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

 * @ignore

 $Id: menu.php 63977 2015-11-13 13:00:23Z diogo.zarpelon $

 $Revision: 28718 $
 $Name$
 $Author: domluc $
 $Date: 2008-03-25 10:00:29 -0300 (Ter, 25 Mar 2008) $

 * Casos de uso: uc-01.01.00
 */
include '../../../../../../config.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

//Bloco para verificar status do sistema
include(CAM_FW_LEGADO."funcoesLegado.lib.php");
include(CAM_FW_LEGADO."sistema.class.php");
if((strtolower(Sistema::consultaStatus()) == 'i')&&(Sessao::read('username') != 'admin')){
    echo("<script type='text/javascript'>parent.window.location.href='".URBEM_ROOT_URL."/index.php?action=sair';</script>");
}

Sessao::open();
Sessao::geraURLRandomica();

$nivel = array_key_exists('nivel',$_GET) ? $_GET['nivel'] : null;
$stTitulo = array_key_exists('stTitulo',$_GET) ? $_GET['stTitulo'] : null;
$stVersao = array_key_exists('stVersao',$_GET) ? $_GET['stVersao'] : null;
$stNomeGestao = array_key_exists('stNomeGestao',$_GET) ? $_GET['stNomeGestao'] : null;
$cod_gestao_pass = array_key_exists('cod_gestao_pass',$_GET) ? $_GET['cod_gestao_pass'] : null;
$cod_modulo_pass = array_key_exists('cod_modulo_pass',$_GET) ? $_GET['cod_modulo_pass'] : null;
$modulos = array_key_exists('modulos',$_GET) ? $_GET['modulos'] : null;

$stNomeGestao = array_key_exists('stNomeGestao',$_GET) ? $stNomeGestao : $stTitulo;
//$stTitulo = array_key_exists('stTitulo',$_GET) ? $stTitulo : $stNomeGestao;

$stGestao = "";

if (Sessao::getExercicio() < date('Y') && isset($_REQUEST['stDataReserva'])) {
    $arData = explode('/', $_REQUEST['stDataReserva']);
    $stData  = $arData[2].'-';
    $stData .= str_pad($arData[1], 2, '0', STR_PAD_LEFT).'-';
    $stData .= str_pad($arData[0], 2, '0', STR_PAD_LEFT);
    Sessao::write('data_reserva_saldo_GF', $stData, true);
}

Sessao::montaTituloPagina( $nivel, $stTitulo);

if ($cod_gestao_pass) {
    $sSQL  = " SELECT                                               \n";
    $sSQL .= "     g.ordem,                                         \n";
    $sSQL .= "     g.cod_gestao,                                    \n";
    $sSQL .= "     g.nom_gestao,                                    \n";
    $sSQL .= "     g.nom_diretorio as diretorio_getao,              \n";
    $sSQL .= "     g.versao                                         \n";
    $sSQL .= " FROM                                                 \n";
    $sSQL .= "     administracao.gestao as g                        \n";
    $sSQL .= " WHERE                                                \n";
    $sSQL .= "     g.cod_gestao = ".$cod_gestao_pass."      \n";
    $sSQL .= " ORDER by                                             \n";
    $sSQL .= "     g.ordem                                     \n";

    $arGestao = array();
    $arGestao[1] = 'GA';
    $arGestao[2] = 'GF';
    $arGestao[3] = 'GP';
    $arGestao[4] = 'GRH';
    $arGestao[5] = 'GT';
    $arGestao[6] = 'GPC';

    $obConexao = new Conexao;
    $obConexao->executaSql( $rsGestoes, $sSQL );
    if ( !$rsGestoes->eof() ) {
        $stDiretorioGestao = trim( $rsGestoes->getCampo("diretorio_getao") );
        $stCaminhoHistorico = $stDiretorioGestao."../../historico.html";
        Sessao::setHistoricoVersao( $stCaminhoHistorico );
    }
}
if ($nivel == 1) {
    Sessao::setVersao( $stVersao );
} elseif ($nivel < 1) {
    Sessao::setVersao( '' );
}

?>

<html>
<head>
<script src="../../../../javaScript/compressed/funcoesJs.js" type="text/javascript"></script>
<script src="../../../../javaScript/compressed/genericas.js" type="text/javascript"></script>
<script type="text/javascript">
      MontaCSSMenu();
      window.status = ":::::::: URBEM ::::::::";
</script>
<script type="text/JavaScript">

function mudaTelaMenu(sPag)
{
    sPagMsg = "mensagem.php?<?=Sessao::getId()?>";
    sPagOc = "limpaSession.inc.php?<?=Sessao::getId()?>";
    //sPagOc = "logoutfecha.php?<?=Sessao::getId()?>"; //Força um erro para debug
    parent.frames["telaPrincipal"].location.replace(sPag);
    parent.frames["telaMensagem"].location.replace(sPagMsg);
    parent.frames["oculto"].location.replace(sPagOc);
}
function mudaMenu(sPag)
{
    document.location.replace(sPag);
}

function EscondeCarregando()
{
    document.getElementById('fundo_carregando').style.visibility='hidden';

    return false;
}
function MostraCarregado()
{
    document.getElementById('fundo_carregando').style.visibility='visible';
}
function mostraTituloPagina(stValor, flVersao)
{
    var stTitulo = "";
    stTitulo += " <table width='100%'>";
    stTitulo += "     <tr>";
    stTitulo += "         <td class='titulocabecalho' height='5' width='100%'>";
    stTitulo += "             <table cellspacing=0 cellpadding=0 class='titulocabecalho_gestao' width='100%'>";
    stTitulo += "                 <tr>";
    stTitulo += "                      <td class='caminho' height='5' width='90%'>";
    stTitulo += "                          " + stValor;
    stTitulo += "                      </td>";
    if (flVersao != '') {
        stTitulo += "                     <td height='5' width='10%' class='versao'>"
        stTitulo += "                       <div class='versaoFundo'>";
<?php

//$stPopUpVersao = CAM_GA_ADM_POPUPS."versao/LSMostrarHistorico.php?".Sessao::getId()."&stArquivoHistorico=".Sessao::getHistoricoVersao();
$stPopUpVersao = Sessao::getHistoricoVersao();

?>
        stTitulo += "                           <a class='versaoFundo' href='javascript: exibeHistorico( \"<?=$stPopUpVersao;?>\" );' STYLE='text-decoration:none'>";
        stTitulo += "                           " + flVersao + "</a>";
        stTitulo += "                         </div>";
        stTitulo += "                     </td>";
    }
    stTitulo += "                </tr>";
    stTitulo += "            </table>";
     stTitulo += "     </td>";
    stTitulo += "     </tr>";
    stTitulo += " </table>";
    if (window.parent.frames["telaPrincipal"].document.getElementById("titulo1") != null) {
        window.parent.frames["telaPrincipal"].document.getElementById("titulo1").innerHTML = stTitulo;
    }

}
function MontraChangelog(stValor)
{
    window.parent.frames["telaPrincipal"].document.getElementById("changelog").innerHTML = stValor;
}

//-->
</script>
<style>
#fundo_carregando {
    width: 99%; /*largura da imagem */
    height: 99%;
    /* As tres regras a seguir posicionam
a imagem na tela */
    position: absolute;
    left: 5px;
    top: 5px;
}

#texto_carregando {
    position: absolute;
    Top: 50px;
    left: 50px;
    font: bold 16px arial, sans-serif;
    color: #000;
    text-align: center;
    padding: 5px 10px;
    /* É na regra CSS para o background que está o FILTRO! */
    /* O filtro é a imagem alpha_branca.gif */
    background-color: #ccc;
}
</style>

<head>

<body bgcolor="#ffffff" leftmargin=0 topmargin=0 >
<?php echo "<script type='text/javascript'>
                window.onload= mostraTituloPagina('".Sessao::getTituloPagina()."','".($nivel ? Sessao::getVersao() : '' )."');
            </script>"; ?>
<div id="fundo_carregando">
<div id="texto_carregando"><!-- document.getElementById('imagem').style.visibility='hidden' -->
<a href=# onClick="javascript:EscondeCarregando();">Fecha</a></div>
</div>

<form name="frmMenu"><?php
if (!Sessao::getId()) {
    echo "<script type='text/javascript'>
                window.location='menu.html'
                </script>";
}

?>
<center>

<table border="0" width="180px" cellspacing="0" cellpadding="0">
<?php
$nivel = (int) $nivel;

switch ($nivel) {
    case 0:
        Sessao::write('acao','', true);

        $sSQL  = " SELECT                                               \n";
        $sSQL .= "     DISTINCT g.ordem,                                \n";
        $sSQL .= "     g.cod_gestao,                                    \n";
        $sSQL .= "     g.nom_gestao,                                    \n";
        $sSQL .= "     g.versao                                         \n";
        $sSQL .= " FROM                                                 \n";
        $sSQL .= "     administracao.gestao as g,                       \n";
        $sSQL .= "     administracao.modulo as m,                       \n";
        $sSQL .= "     administracao.funcionalidade as f,               \n";
        $sSQL .= "     administracao.acao as a,                         \n";
        $sSQL .= "     administracao.permissao as p                     \n";
        $sSQL .= " WHERE                                                \n";
        $sSQL .= "     g.cod_gestao = m.cod_gestao AND                  \n";
        $sSQL .= "     m.cod_modulo = f.cod_modulo AND                  \n";
        $sSQL .= "     f.cod_funcionalidade = a.cod_funcionalidade AND  \n";
        $sSQL .= "     a.cod_acao = p.cod_acao AND                      \n";
        $sSQL .= "     p.ano_exercicio = '".Sessao::getExercicio()."' AND     \n";
        $sSQL .= "     p.numcgm=".Sessao::read('numCgm')." AND                 \n";
        $sSQL .= "     m.cod_modulo > 0                                 \n";
        $sSQL .= " ORDER by                                             \n";
        $sSQL .= "     g.ordem                                          \n";

        $obConexao = new Conexao;
        $obConexao->executaSql( $rsGestoes, $sSQL );

        if (Sessao::getExercicio() < date('Y')) {
            $stPerguntaGF = 'true';
        } else {
            $stPerguntaGF = 'false';
        } while ( !$rsGestoes->eof() ) {
            $stNomGestao = trim( $rsGestoes->getCampo("nom_gestao") );
            $inCodGestao = trim( $rsGestoes->getCampo("cod_gestao") );
            $stVersao    = trim( $rsGestoes->getCampo("versao") );

            if ($inCodGestao != 2) {
                $stGestao .= '<tr><td width="100%"><a href="menu.php?'.Sessao::getId().'&nivel=1&cod_gestao_pass='.$inCodGestao.'
&stTitulo='.$stNomGestao.'&stVersao='.$stVersao.'" class=menu>'.$stNomGestao.'</a></font></td></tr>';
            } elseif ($inCodGestao == 2 && $stPerguntaGF == 'false') {
                $stGestao .= '<tr><td width="100%"><a href="menu.php?'.Sessao::getId().'&nivel=1&cod_gestao_pass='.$inCodGestao.'
&stTitulo='.$stNomGestao.'&stVersao='.$stVersao.'" class=menu>'.$stNomGestao.'</a></font></td></tr>';
            } else {
                $stGestao .= '<tr><td width="100%">
                                <a href="#" onClick="mudaTelaMenu(\'inicial.php?<?=Sessao::getId();?>&nivel=1&reservaSaldo=true\');"
                                class=menu>'.$stNomGestao.'
                                </a></font>
                              </td></tr>';
            }

            $rsGestoes->proximo();
        }
        ?>
    <tr>
        <td width="100%"><span class="menutitle">
        Gestões </span></td>
    </tr>
    <?=$stGestao;?>
    <?php
    break;
    //MODULO
    // setar gestao atual
    Sessao::write('inGestaoAtual', $cod_gestao_pass, true);
    Sessao::write('acao','', true);
case 1:
    Sessao::write('acao','', true);
    if ($cod_gestao_pass == 4) {
//         //O controle de entidades do GRH só será liberado a partir da versão 1.40.0 da GRH
//         $inVersaoGRH = str_replace(".","",VERSAO_GRH);
//         if ($inVersaoGRH >= 1400) {
            $stPagina = CAM_GRH_ENT_INSTANCIAS."entidadeUsuario/FMEntidadeUsuario.php?".Sessao::getId()."&inCodGestao=".$cod_gestao_pass."&stNomeGestao=".$stNomeGestao."&stTitulo=".$stTitulo."&stVersao=".$stVersao;
            sistemaLegado::executaFrameOculto("parent.frames['telaPrincipal'].location.replace('".$stPagina."');");
//         }
    }

    $sSQL  = "   SELECT DISTINCT m.ordem                                    \n";
    $sSQL .= "        , m.cod_modulo                                        \n";
    $sSQL .= "        , m.nom_modulo                                        \n";
    $sSQL .= "        , g.nom_diretorio                                     \n";
    $sSQL .= "        , g.versao                                            \n";
    $sSQL .= "     FROM administracao.gestao         AS g                   \n";
    $sSQL .= "        , administracao.modulo         AS m                   \n";
    $sSQL .= "        , administracao.funcionalidade AS f                   \n";
    $sSQL .= "        , administracao.acao           AS a                   \n";
    $sSQL .= "        , administracao.permissao      AS p                   \n";
    $sSQL .= "    WHERE g.cod_gestao         = m.cod_gestao                 \n";
    $sSQL .= "      AND p.numcgm             = ".Sessao::read('numCgm')."   \n";
    $sSQL .= "      AND m.cod_modulo         = f.cod_modulo                 \n";
    $sSQL .= "      AND f.cod_funcionalidade = a.cod_funcionalidade         \n";
    $sSQL .= "      AND a.cod_acao           = p.cod_acao                   \n";
    $sSQL .= "      AND p.ano_exercicio      = '".Sessao::getExercicio()."' \n";
    $sSQL .= "      AND m.cod_gestao         = ".$cod_gestao_pass."         \n";
    $sSQL .= "      AND m.cod_modulo         > 0                            \n";
    $sSQL .= "      AND m.ativo              = TRUE                         \n";
    $sSQL .= " ORDER BY m.ordem                                             \n";

    $obConexao = new Conexao;
    $obConexao->executaSql( $rsModulos, $sSQL );
    $stModulo = '';

    while ( !$rsModulos->eof() ) {

        $stDiretorioGestao = $rsModulos->getCampo('nom_diretorio');
        $modulos  = trim($rsModulos->getCampo("nom_modulo"));
        $cod_modulo  = trim($rsModulos->getCampo("cod_modulo"));
        if ($stNomeGestao == '') {
            $stNomeGestao = $_REQUEST['stNomeGestao'];
        }
        $stVersaoGRH = array_key_exists('stVersao',$_GET) ? $_GET['stVersao'] : null;
        $inVersaoGRH = (int) str_replace(".", "", $stVersaoGRH);

        if ($inVersaoGRH >= 1400) {
            if ($cod_gestao_pass == 4 AND Sessao::getBoEntidade() == false ) {
                $stModulo .= '<tr><td width="100%"><a href="#" class=menu>'.$modulos.'</a></font></td></tr>';
            } else {
                $stModulo .= '<tr><td width="100%"><a
    href="menu.php?'.Sessao::getId().'&nivel=2&cod_modulo_pass='.$cod_modulo.'
    &cod_gestao_pass='.$cod_gestao_pass.'&stTitulo='.$modulos.'&stNomeGestao='.$stNomeGestao.'" class=menu>'.$modulos.'</a></font></td></tr>';
            }
        } else {
            $stModulo .= '<tr><td width="100%"><a
    href="menu.php?'.Sessao::getId().'&nivel=2&cod_modulo_pass='.$cod_modulo.'
    &cod_gestao_pass='.$cod_gestao_pass.'&stTitulo='.$modulos.'&stNomeGestao='.$stNomeGestao.'" class=menu>'.$modulos.'</a></font></td></tr>';
        }
        $rsModulos->proximo();
        $stChangelog = $stDiretorioGestao."../../historico.txt";
    }
    ?>

    <tr>
        <td width="100%"><a id="vFunc" href="menu.php?<?=Sessao::getId();?>"
            onClick="mudaTelaMenu('inicial.php?<?=Sessao::getId();?>&nivel=1');"
            class="menutitle">Gestões</a></td>
    </tr>

    <tr>
        <td width="100%"><span class="menutitlePenultimoNivel"><?php echo $stNomeGestao;?>
        </span></td>
    </tr>
    <?=$stModulo;?>

    <!-- Nivel 2-->
    <?php
    break;
    //FUNCIONALIDADE
case 2:
    Sessao::write('acao','', true);
    Sessao::write('modulo',$cod_modulo_pass, true);
    $sSQL  = "   SELECT f.ordem                                                  \n";
    $sSQL .= "        , f.cod_funcionalidade                                     \n";
    $sSQL .= "        , f.nom_funcionalidade                                     \n";
    $sSQL .= "        , m.nom_modulo                                             \n";
    $sSQL .= "     FROM administracao.modulo         AS m                        \n";
    $sSQL .= "        , administracao.funcionalidade AS f                        \n";
    $sSQL .= "        , (                                                        \n";
    $sSQL .= "              SELECT a.cod_funcionalidade                          \n";
    $sSQL .= "                FROM administracao.acao      AS a                  \n";
    $sSQL .= "                   , administracao.permissao AS p                  \n";
    $sSQL .= "               WHERE a.cod_acao      = p.cod_acao                  \n";
    $sSQL .= "                 AND p.numcgm        = ".Sessao::read('numCgm')."  \n";
    $sSQL .= "                 AND p.ano_exercicio = '".Sessao::getExercicio()."'\n";
    $sSQL .= "            GROUP BY a.cod_funcionalidade                          \n";
    $sSQL .= "          ) AS a                                                   \n";
    $sSQL .= "    WHERE m.cod_modulo         = f.cod_modulo                      \n";
    $sSQL .= "      AND f.cod_funcionalidade = a.cod_funcionalidade              \n";
    $sSQL .= "      AND f.cod_modulo         = ".$cod_modulo_pass."              \n";
    $sSQL .= "      AND f.ativo              = TRUE                              \n";
    $sSQL .= " ORDER By f.ordem                                                  \n";

    $obConexao = new Conexao;
    $obConexao->executaSql( $rsFuncionalidade, $sSQL );

    $gera_menuf = "";
    while ( !$rsFuncionalidade->eof() ) {

        $funcao     = trim($rsFuncionalidade->getCampo("nom_funcionalidade"));
        $cod_funcao = trim($rsFuncionalidade->getCampo("cod_funcionalidade"));
        $modulos    = trim($rsFuncionalidade->getCampo("nom_modulo"));
        $gera_menuf .= '<tr><td width="100%"><a
href="menu.php?'.Sessao::getId().'&nivel=3&cod_func_pass='.$cod_funcao.'&cod_gestao_pass='.$cod_gestao_pass.'
&stTitulo='.$funcao.'&stNomeGestao='.$_GET['stNomeGestao'].'&modulos='.$modulos.'" class=menu>'.$funcao.'</a></td></tr>';
        $rsFuncionalidade->proximo();
    }
    ?>
    <tr>
        <td width="100%"><a id="vFunc" href="menu.php?<?=Sessao::getId();?>"
            onClick="mudaTelaMenu('inicial.php?<?=Sessao::getId();?>&nivel=0');"
            class="menutitle"> Gestões</a></td>
    </tr>

    <tr>
        <td width="100%"><?php
        $stLinkMenu  = Sessao::getId()."&nivel=1&stNomeGestao=".$stNomeGestao."&modulos=".$modulos."&cod_gestao_pass=".$cod_gestao_pass;
        $stLinkMenu .= "&stVersao=".Sessao::getVersao();
        ?> <a id="vFunc" href="menu.php?<?=$stLinkMenu;?>" class="menutitle"
            onClick="mudaTelaMenu('inicial.php?<?=Sessao::getId();?>&nivel=1&cod_gestao_pass=<?=$cod_gestao_pass;?>');">
                        <?php
                        if ($stNomeGestao != '') {
                echo $stNomeGestao;
            } else {
                $stNomeGestao = $_REQUEST['stNomeGestao'];
                echo $stNomeGestao;
            }
            ?> </a></td>
    </tr>

    <tr>
        <td width="100%"><span class="menutitlePenultimoNivel"><?php echo $modulos; ?>
        </span></td>
    </tr>
    <?=$gera_menuf;?>
    <!--Nivel 3-->
    <?php
    break;
    //ACAO
case 3:
    //Se for passado uma acao, ele nao limpa a sessao
    if (isset($_REQUEST['acao']) && $_REQUEST['acao'] != '') {
        Sessao::write('acao',$_REQUEST['acao'], true);
    } else {
        Sessao::write('acao','', true);
    }

    $cod_func_pass = $_GET['cod_func_pass'];
    $sSQL  = "   SELECT DISTINCT a.ordem                                   \n";
    $sSQL .= "        , m.cod_modulo                                       \n";
    $sSQL .= "        , m.nom_modulo                                       \n";
    $sSQL .= "        , f.cod_funcionalidade                               \n";
    $sSQL .= "        , f.nom_funcionalidade                               \n";
    $sSQL .= "        , a.nom_acao                                         \n";
    $sSQL .= "        , a.nom_arquivo                                      \n";
    $sSQL .= "        , a.parametro                                        \n";
    $sSQL .= "        , a.complemento_acao                                 \n";
    $sSQL .= "        , f.nom_diretorio as func_dir                        \n";
    $sSQL .= "        , m.nom_diretorio as mod_dir                         \n";
    $sSQL .= "        , g.nom_diretorio as gest_dir                        \n";
    $sSQL .= "        , a.cod_acao                                         \n";
    $sSQL .= "     FROM administracao.gestao         AS g                  \n";
    $sSQL .= "        , administracao.modulo         AS m                  \n";
    $sSQL .= "        , administracao.funcionalidade AS f                  \n";
    $sSQL .= "        , administracao.acao           AS a                  \n";
    $sSQL .= "        , administracao.permissao      AS p                  \n";
    $sSQL .= "    WHERE g.cod_gestao         = m.cod_gestao                \n";
    $sSQL .= "      AND m.cod_modulo         = f.cod_modulo                \n";
    $sSQL .= "      AND f.cod_funcionalidade = a.cod_funcionalidade        \n";
    $sSQL .= "      AND a.cod_acao           = p.cod_acao                  \n";
    $sSQL .= "      AND a.cod_funcionalidade = ".$cod_func_pass."          \n";
    $sSQL .= "      AND p.numcgm             = ".Sessao::read('numCgm')."  \n";
    $sSQL .= "      AND p.ano_exercicio      = '".Sessao::getExercicio()."'\n";
    $sSQL .= "      AND a.ativo              = TRUE                        \n";
    $sSQL .= " ORDER BY a.ordem                                            \n";

    $obConexao = new Conexao;
    $obConexao->executaSql( $rsAcao, $sSQL );
    $gera_menua = "";
    while ( !$rsAcao->eof() ) {
        $nomeacao              = trim($rsAcao->getCampo("nom_acao"));
        $complemento_acao      = trim($rsAcao->getCampo("complemento_acao"));
        $nomeaquivo            = trim($rsAcao->getCampo("nom_arquivo"));
        $nomDirGestao          = trim($rsAcao->getCampo("gest_dir"));
        $nomedir1              = trim($rsAcao->getCampo("mod_dir"));
        $nomedir2              = trim($rsAcao->getCampo("func_dir"));
        $funcaoname            = trim($rsAcao->getCampo("nom_funcionalidade"));
        $inCodFuncionalidade   = trim($rsAcao->getCampo("cod_funcionalidade"));
        $nomemodular           = trim($rsAcao->getCampo("nom_modulo"));
        $numacao               = trim($rsAcao->getCampo("cod_acao"));
        $nummodular            = trim($rsAcao->getCampo("cod_modulo"));
        //Sessao::write('modulo',$nummodular);

        $parametro     = trim($rsAcao->getCampo("parametro"));

        $stCaminho = $nomDirGestao.$nomedir1.$nomedir2.$nomeaquivo;
        $stCaminho .= '?'.Sessao::getId().'&acao='.$numacao.'&stAcao='.$parametro.'&modulo='.$nummodular;
        $stCaminho .= '&funcionalidade='.$inCodFuncionalidade.'&nivel=1&cod_gestao_pass='.$cod_gestao_pass;
        $stCaminho .= '&stNomeGestao='.$stNomeGestao.'&modulos='.$modulos.'';
        $gera_menua .= '<tr>
                     <td width="100%">
                          <a href='.$stCaminho.' target="telaPrincipal" class=menu title=\''.$complemento_acao.'\' onClick="mudaTelaMenu(\''.$stCaminho.'\');"  >'.$nomeacao.'</a>
                      </td>
                   </tr>';
        $rsAcao->proximo();
    }

    $stNomeGestao = $_GET['stNomeGestao'];
    $modulos = $_GET['modulos'];
    ?>
    <tr>
        <td width="100%"><a id="vFunc" href="menu.php?<?=Sessao::getId();?>"
            class="menutitle"
            onClick="mudaTelaMenu('inicial.php?<?=Sessao::getId();?>&nivel=0');">
        Gestões</a></td>
    </tr>

    <tr>
        <td width="100%"><?php
        $stLinkMenu  = Sessao::getId()."&nivel=1&stNomeGestao=".$stNomeGestao."&modulos=".$modulos."&cod_gestao_pass=".$cod_gestao_pass;
        $stLinkMenu .= "&stVersao=".Sessao::getVersao();
        ?> <a id="vMod" href="menu.php?<?=$stLinkMenu;?>" class="menutitle"
            onClick="mudaTelaMenu('inicial.php?<?=Sessao::getId();?>&nivel=1&stNomeGestao=<?=$stNomeGestao;?>&modulos=<?$modulos;?>');">
        <?php echo $stNomeGestao; ?></a></td>
    </tr>

    <tr>
        <td width="100%"><a id="vFunc"
            href="menu.php?<?=Sessao::getId();?>&nivel=2&cod_modulo_pass=<?=$nummodular?>&stNomeGestao=<?=$stNomeGestao;?>
&modulos=<?=$modulos;?>&cod_gestao_pass=<?=$cod_gestao_pass;?>"
            onClick="mudaTelaMenu('inicial.php?<?=Sessao::getId();?>&nivel=2&stNomeGestao=<?=$stNomeGestao;?>
&modulos=<?=$modulos;?>&cod_modulo_pass=<?=$nummodular;?>
&cod_gestao_pass=<?=$cod_gestao_pass;?>');"
            class="menutitle"><?php echo $modulos; ?></a></td>
    </tr>

    <tr>
        <td width="100%"><span class="menutitlePenultimoNivel"><?php echo $funcaoname; ?>
        </b></span></td>
    </tr>
    <?=$gera_menua;?>
    <?php
}
?>
    <tr><td class="partebaixo"></td></tr>
</table>
</td>
</tr>
</table>

</center>
</form>
<script type="text/javascript">
    EscondeCarregando();
</script>
</body>
</html>
