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
* Manutneção do sistema
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 15569 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 07:25:15 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.03.91
*/

    include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include(CAM_FW_LEGADO."funcoesLegado.lib.php");
    include(CAM_FW_LEGADO."sistema.class.php");
    include(CAM_FW_LEGADO."paginacaoLegada.class.php");

    setAjuda( "UC-01.03.91" );
?>
<script type="text/javascript">
     function zebra(id, classe)
     {
            var tabela = document.getElementById(id);
            var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
                ((i%2) == 0) ? linhas[i].className = classe : void(0);
            }
        }
</script>
<?php
    $ctrl = $_REQUEST['ctrl'];

    if (!(isset($ctrl))) {
        $ctrl = 0;

    }

    switch ($ctrl) {
        case 0:

            $pagina = $_REQUEST['pagina'];

            $select = " SELECT
                              gestao.nom_gestao,
                              M.cod_modulo,
                              M.nom_modulo,
                              U.numcgm,
                              U.username,
                              recuperadescricaoorgao(U.cod_orgao, now()::date) as nom_setor

                        FROM  administracao.gestao,
                              administracao.modulo  AS M,
                              administracao.usuario AS U,
                              organograma.orgao

                       WHERE  gestao.cod_gestao  = M.cod_gestao
                         AND  M.cod_responsavel  = U.numcgm
                         AND  U.cod_orgao        = orgao.cod_orgao
                         AND  M.cod_modulo       <> 0 ";

            $selectSessao  = Sessao::read('select');

            if ($selectSessao == "") {
                Sessao::write('select', $select);
            }

            $ordem = "gestao.nom_gestao, M.nom_modulo";

            $sSQL = Sessao::read('select');

            $paginacao = new paginacaoLegada;
            $paginacao->pegaDados( $sSQL ,"10");
            $paginacao->pegaPagina($pagina);
            $paginacao->geraLinks();
            $paginacao->pegaOrder($ordem, "ASC");
            $sSQL = $paginacao->geraSQL();

            $count = $paginacao->contador();
            $conn = new dataBaseLegado;
            $conn->abreBd();
            $conn->abreSelecao($sSQL);

            echo
                "<table width='100%' id='processos'>
                    <tr>
                        <td class='alt_dados' colspan='6'>
                            Módulos
                        </td>
                    </tr>
                    <tr>
                        <td class='labelcentercabecalho'>
                            &nbsp;
                        </td>
                        <td class='labelcentercabecalho'>
                            Gestão
                        </td>
                        <td class='labelcentercabecalho'>
                            Módulo
                        </td>
                        <td class='labelcentercabecalho'>
                            Responsável
                        </td>
                        <td class='labelcentercabecalho'>
                            Setor
                        </td>
                        <td class='labelcentercabecalho'>
                            &nbsp;
                        </td>
                    </tr>";
            while (!($conn->eof())) {
                $stGestao   = $conn->pegaCampo("nom_gestao");
                $codModulo  = $conn->pegaCampo("cod_modulo");
                $modulo     = $conn->pegaCampo("nom_modulo");
                $codUsuario = $conn->pegaCampo("numcgm");
                $username   = $conn->pegaCampo("username");
                $setor      = $conn->pegaCampo("nom_setor");
                echo
                    "<tr>
                        <td class=labelcenter>
                            ".$count++."
                        </td>
                        <td class=show_dados>
                           ".$stGestao."
                        </td>
                        <td class=show_dados>
                            ".$modulo."
                        </td>
                        <td class=show_dados>
                            ".$username."
                        </td>
                        <td class=show_dados>
                            ".$setor."
                        </td>
                        <td class='botao'>
                            <a href='".$PHP_SELF."?".Sessao::getId()."&pagina=".
                            $pagina."&modulo=".$modulo."&username=".
                            $username."&setor=".$setor."&ctrl=1&codUsuario=".
                            $codUsuario."&ordem=".$ordem."&codModulo=".$codModulo."'>
                                <img src='".CAM_FW_IMAGENS."btneditar.gif' border=0>
                            </a>
                        </td>
                    </tr>";
                $conn->vaiProximo();
            }
            echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
                $paginacao->mostraLinks();
            echo "</font></tr></td></table>";
?>
        <script>zebra('processos','zb');</script>
<?php
        break;
        case 1:

            $modulo = $_REQUEST['modulo'];
            $pagina = $_REQUEST['pagina'];
            $ordem = $_REQUEST['ordem'];
            $codModulo = $_REQUEST['codModulo'];
            $codUsuario = $_REQUEST['codUsuario'];
            $username = $_REQUEST['username'];

            ?>
       <script type="text/javascript">
            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;
                var campoaux;

                campo = document.frm.usuarioResponsavel.value.length;
                if (campo == 0) {
                    mensagem += "@Responsável";
                    erro = true;
                }
                if (erro) alertaAviso(mensagem,'formulario','erro','<?=Sessao::getId()?>');
                    return !(erro);
            }
            function Salvar()
            {
                if (Valida()) {
                    document.frm.action = "responsavelModulo.php?<?=Sessao::getId()?>&ctrl=2";
                    document.frm.submit();
                }
            }
            function Cancela()
            {
                document.frm.action = "responsavelModulo.php?<?=Sessao::getId()?>&ctrl=0&pagina=<?=$pagina?>";
                document.frm.submit();
            }
            function buscaResponsavel()
            {
                var targetTmp = document.frm.target;
                var actionTmp = "responsavelModulo.php?<?=Sessao::getId()?>&ctrl=1";
                document.frm.action += "&ctrl=3";
                document.frm.target  = "oculto";
                document.frm.submit();
                document.frm.action = actionTmp;
                document.frm.target = targetTmp;
            }
       </script>
       <form name="frm" action="responsavelModulo.php?<?=Sessao::getId()?>" method="post">
            <input type="hidden" name="pagina" value="<?=$pagina?>">
            <input type="hidden" name="ordem" value="<?=$ordem?>">
            <input type="hidden" name="modulo" value="<?=$modulo?>">
            <input type="hidden" name="codModulo" value="<?=$codModulo?>">
            <table width="100%">
                <tr>
                    <td class=alt_dados colspan="2">
                        Dados para responsável por módulo
                    </td>
                </tr>
                <tr>
                    <td class=label>
                        Módulo
                    </td>
                    <td class=field>
                        <?=$modulo?>
                    </td>
                <tr>
                    <td class=label title="Usuário responsável pelo módulo">
                        *Responsável
                    </td>
                    <td class=field>
                        <?php geraCampoInteiro("usuarioResponsavel",10,10,$codUsuario, false,"onchange='javascript:buscaResponsavel();'"); ?>
                        <?php geraCampoInteiro("usuarioResponsavelN",40,40,$username, true); ?>
                            <a href="#"
                                onClick="procurarCgm('frm','usuarioResponsavel','usuarioResponsavelN','usuario','<?=Sessao::getId();?>');">
                                <img src="<?=CAM_FW_IMAGENS.'procuracgm.gif'?>" alt=""
                                border=0>
                            </a>
                    </td>
                </tr>
                <tr>
                    <td class=field colspan=2>
                        <?=geraBotaoOk(1,0,1,1);?>
                    </td>
                </tr>
            </table>
       </form>
            <?php
        break;
        case 2:

            $usuarioResponsavel = $_REQUEST['usuarioResponsavel'];
            $usuarioResponsavelN = $_REQUEST['usuarioResponsavelN'];
            $codModulo = $_REQUEST['codModulo'];
            $modulo = $_REQUEST['modulo'];
            $pagina = $_REQUEST['pagina'];
            $ordem = $_REQUEST['ordem'];

            $userMod = new sistema;

            if ($userMod->defineResponsavel($usuarioResponsavel, $codModulo)) {   //Testa se usuário pode ser dono do módulo e retorna o erro.

                include CAM_FW_LEGADO."auditoriaLegada.class.php";
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $modulo);
                $audicao->insereAuditoria();
                echo '
                <script type="text/javascript">
                alertaAviso("'.$usuarioResponsavelN.' Definido como Responsável pelo Módulo: '.$modulo.'","unica","aviso","'.Sessao::getId().'");
                mudaTelaPrincipal("'.$_SERVER['PHP_SELF'].'?'.Sessao::getId().'&pagina='.$pagina.'&ordem='.$ordem.'");
                </script>';
            } else {
                echo '
                <script type="text/javascript">
                alertaAviso("Erro ao tentar incluir o usuário: '.$usuarioResponsavelN.' como responsavel pelo Módulo: '.$modulo.'","unica","erro","'.Sessao::getId().'");
                mudaTelaPrincipal("'.$_SERVER['PHP_SELF'].'?'.Sessao::getId().'&pagina='.$pagina.'&ordem='.$ordem.'");
                </script>';
            }
        break;
        case 3:

            $usuarioResponsavel = $_REQUEST['usuarioResponsavel'];

            $sSQL = "   SELECT
                            username
                        FROM
                            administracao.usuario
                        WHERE
                            numcgm = ".$usuarioResponsavel;
            $conn = new dataBaseLegado;
            $conn->abreBd();
            $conn->abreSelecao($sSQL);
            $usuario = $conn->pegaCampo("username");
            $conn->limpaSelecao();
            $conn->fechaBd();
            if ($usuario) {
                $js = "f.usuarioResponsavelN.value = '".$usuario."';";
                executaFrameOculto($js);
            } else {
                SistemaLegado::exibeAviso('Não existe usuário cadastrado com o CGM '.$usuarioResponsavel.'!', 'consultar', 'erro' );
            }
        break;
    }
?>
