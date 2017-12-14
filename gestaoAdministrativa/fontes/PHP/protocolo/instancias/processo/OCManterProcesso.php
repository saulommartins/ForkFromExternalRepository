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
    * Arquivo de instância para manutenção de normas
    * Data de Criação: 04/09/2005

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    Casos de uso: uc-01.06.98

    $Id: OCManterProcesso.php 59612 2014-09-02 12:00:51Z gelson $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {
    case 'selecionarAcao':
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php");
        $obTAdministracaoAcao = new TAdministracaoAcao();
        $stFiltro = ' AND A.cod_acao ='.$_REQUEST['inCodigoAcao'];
        $obTAdministracaoAcao->recuperaCaminhoAcao($rsAcao,$stFiltro);
        $stPagina = trim($rsAcao->getCampo('caminho')).'?'.Sessao::getId();

        $stPaginaAcao  = $stPagina."stAcao=".$rsAcao->getCampo('parametro')."&nivel=1&modulo=".$rsAcao->getCampo('cod_modulo');
        $stPaginaAcao .= "&funcionalidade=".$rsAcao->getCampo('cod_funcionalidade')."&modulos=".$rsAcao->getCampo('nom_modulo');;
        $stPaginaAcao .= "&cod_gestao_pass=".$rsAcao->getCampo('cod_gestao')."&stNomeGestao=".$rsAcao->getCampo('nom_gestao');

        $stPaginaMenu  = CAM_FW_INSTANCIAS.'index/menu.php?'.Sessao::getId().'&nivel=3';
        $stPaginaMenu .= '&cod_func_pass='.$rsAcao->getCampo('cod_funcionalidade');
        $stPaginaMenu .= '&cod_gestao_pass='.$rsAcao->getCampo('cod_gestao');
        $stPaginaMenu .= '&stTitulo='.$rsAcao->getCampo('nom_funcionalidade');
        $stPaginaMenu .= '&stNomeGestao='.$rsAcao->getCampo('nom_gestao').'&modulos='.$rsAcao->getCampo('nom_modulo');

        $stJs  = "parent.frames['telaMenu'].location.replace('$stPaginaMenu');\n";//MENU
        $stJs .= "parent.frames['telaPrincipal'].location.replace('$stPaginaAcao');\n";
        $stJs .= "var stPagMsg  = '".CAM_FW_INSTANCIAS."index/mensagem.php?".Sessao::getId()."';\n";
        $stJs .= "parent.frames['telaMensagem'].location.replace(stPagMsg);\n";

        $stJs .= "var stTituloPagina = '$stTituloPagina';\n";
        $stJs .= "if ( stTituloPagina !=  window.parent.frames['telaPrincipal'].document.getElementById('titulo').innerHTML ) {\n";
        $stJs .= "    window.parent.frames['telaPrincipal'].document.getElementById('titulo').innerHTML = '$stTituloPagina';\n";
        $stJs .= "}\n";
        $stJs .= "var stPagLimpaSessao  = '".CAM_FW_INSTANCIAS."index/limpaSession.php?".Sessao::getId()."';\n";
        $stJs .= "parent.frames['oculto'].location.replace(stPagLimpaSessao);\n";

        $arInteressados    = Sessao::getRequestProtocolo();
        $arAuxInteressados = $arInteressados['interessados'];

        $arDadosProtocolo = $_REQUEST;
        $arDadosProtocolo['interessados'] = $arAuxInteressados;

        Sessao::setRequestProtocolo($arDadosProtocolo);
        Sessao::setVoltarProtocolo( true );
        Sessao::write('acao',$rsAcao->getCampo('cod_acao'));
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case 'voltarProcesso':
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php");
        $obTAdministracaoAcao = new TAdministracaoAcao();
        $stFiltro = ' AND A.cod_acao = 57 ';
        $obTAdministracaoAcao->recuperaCaminhoAcao($rsAcao,$stFiltro);
        $stPagina = $rsAcao->getCampo('caminho').'?'.Sessao::getId();

        $stPaginaAcao  = $stPagina."stAcao=".$rsAcao->getCampo('parametro')."&nivel=1&modulo=".$rsAcao->getCampo('cod_modulo');
        $stPaginaAcao .= "&funcionalidade=".$rsAcao->getCampo('cod_funcionalidade')."&modulos=".$rsAcao->getCampo('nom_modulo');;
        $stPaginaAcao .= "&cod_gestao_pass=".$rsAcao->getCampo('cod_gestao')."&stNomeGestao=".$rsAcao->getCampo('nom_gestao');

        $stPaginaMenu  = CAM_FW_INSTANCIAS.'index/menu.php?'.Sessao::getId().'&nivel=3';
        $stPaginaMenu .= '&cod_func_pass='.$rsAcao->getCampo('cod_funcionalidade');
        $stPaginaMenu .= '&cod_gestao_pass='.$rsAcao->getCampo('cod_gestao');
        $stPaginaMenu .= '&stTitulo='.$rsAcao->getCampo('nom_funcionalidade');
        $stPaginaMenu .= '&stNomeGestao='.$rsAcao->getCampo('nom_gestao').'&modulos='.$rsAcao->getCampo('nom_modulo');

        $stJs  = "parent.frames['telaMenu'].location.replace('$stPaginaMenu');\n";//MENU
        $stJs .= "parent.frames['telaPrincipal'].location.replace('$stPaginaAcao');\n";
        $stJs .= "var stPagMsg  = '".CAM_FW_INSTANCIAS."index/mensagem.php?".Sessao::getId()."';\n";
        $stJs .= "parent.frames['telaMensagem'].location.replace(stPagMsg);\n";

        $stJs .= "var stTituloPagina = '$stTituloPagina';\n";
        $stJs .= "if ( stTituloPagina !=  window.parent.frames['telaPrincipal'].document.getElementById('titulo').innerHTML ) {\n";
        $stJs .= "    window.parent.frames['telaPrincipal'].document.getElementById('titulo').innerHTML = '$stTituloPagina';\n";
        $stJs .= "}\n";
        $stJs .= "var stPagLimpaSessao  = '".CAM_FW_INSTANCIAS."index/limpaSession.php?".Sessao::getId()."';\n";
        $stJs .= "parent.frames['oculto'].location.replace(stPagLimpaSessao);\n";

        Sessao::write('acao',$rsAcao->getCampo('cod_acao'));
        SistemaLegado::executaFrameOculto($stJs);
    break;
}
?>
