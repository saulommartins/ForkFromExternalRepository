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
    * Altera as Naturezas no sistema de PATRIMÔNIO
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Leonardo Tremper
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 13075 $
    $Name$
    $Autor: $
    $Date: 2006-07-21 08:36:18 -0300 (Sex, 21 Jul 2006) $

    * Casos de uso: uc-03.01.03
*/

/*
$Log$
Revision 1.22  2006/07/21 11:35:56  fernando
Inclusão do  Ajuda.

Revision 1.21  2006/07/13 20:23:19  fernando
Alteração de hints

Revision 1.20  2006/07/13 13:51:21  fernando
Alteração de hints

Revision 1.19  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.18  2006/07/06 12:11:28  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

setAjuda("UC-03.01.03");
if (!(isset($ctrl)))
    $ctrl = 0;

switch ($ctrl) {

    // lista Naturezas cadastradas
    case 0:

        if (isset($acao)) {
            $sSQLs = "SELECT cod_natureza, nom_natureza FROM patrimonio.natureza";
            $sessao->transf = $sSQLs;
        }

        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';

        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sessao->transf,"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("nom_natureza","ASC");
        $sSQL = $paginacao->geraSQL();

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();

?>
        <table width="100%">
        <tr>
            <td class="alt_dados" colspan="4">Registros da Natureza</td>
        </tr>
        <tr>
            <td class="labelcenter" width="5%">&nbsp;</td>
            <td class="labelcenter" width="12%">Código</td>
            <td class="labelcenter" width="80%">Descrição</td>
            <td class="labelcenter">&nbsp;</td>
        </tr>
<?php
        $cont = $paginacao->contador();

        while (!$dbEmp->eof()) {
                $codNaturezaf  = trim($dbEmp->pegaCampo("cod_natureza"));
                $nomNaturezaf  = trim($dbEmp->pegaCampo("nom_natureza"));
                $dbEmp->vaiProximo();
?>
                <tr>
                    <td class="labelcenter"><?=$cont++;?></td>
                    <td class="show_dados_right">&nbsp;<?=$codNaturezaf;?></td>
                    <td class="show_dados">&nbsp;<?=$nomNaturezaf;?></td>
                    <td class="botao" title="Alterar">
                        <a href='alteraNatureza.php?<?=Sessao::getId();?>&codNatureza=<?=$codNaturezaf;?>&ctrl=1'>
                        <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btneditar.gif'  border="0"></a>
                    </td>
                </tr>
<?php
                echo "\n";
        }
?>
        </table>
<?php

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();

?>
        <table width="100%" align="center">
        <tr>
            <td align="center">
                <font size=2><?=$paginacao->mostraLinks(); ?></font>
            </td>
        </tr>
        </table>
<?php

    break;

    // exibi formulario para alteracao da Natureza selecionada
    case 1:

        //****************************************************************************
        $sSQL = "select cod_natureza, nom_natureza from patrimonio.natureza WHERE cod_natureza = ".$codNatureza;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();

        $codNatureza = trim($dbEmp->pegaCampo("cod_natureza"));
        $nomNatureza  = trim($dbEmp->pegaCampo("nom_natureza"));

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        //****************************************************************************
    ?>
        <script type="text/javascript">

        function Valida()
        {
            var mensagem = "";
            var erro = false;
            var campo;
            var campoaux;

            campo = trim(document.frm.nomNatureza.value).length;
                if (campo == 0) {
                mensagem += "@O campo Descrição da Natureza é obrigatório.";
                erro = true;
            }

                if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                return !(erro);
        }

        function Salvar()
        {
            if (Valida()) {
                document.frm.submit();
            }
        }

        function Cancela()
        {
                mudaTelaPrincipal("alteraNatureza.php?<?=Sessao::getId();?>&ctrl=0&pagina=<?=$pagina;?>");
            }
        </script>

        <form name="frm" action="alteraNatureza.php?<?=Sessao::getId()?>" method="POST">

        <table width="100%">

        <tr>
            <td colspan="2" class="alt_dados">Dados da Natureza</td>
        </tr>

        <tr>
            <td class="label" width="20%">Código</td>
            <td class="field" width="80%"><?=$codNatureza;?></td>
        </tr>

        <tr>
            <td class="label" title="Informe a descrição da natureza do bem.">*Descrição da Natureza</td>
            <td class=field>
                <input type="text" name="nomNatureza" size="80" maxlength="80" value="<?=$nomNatureza;?>">
                <input type="hidden" name="codNatureza" value="<?=$codNatureza;?>">
                <input type="hidden" name="ctrl" value="2">
            </td>
        </tr>

        <tr>
            <td class="field" colspan="2">
                <?=geraBotaoAltera();?>
            </td>
        </tr>

        </table>

        </form>
<?php
    break;

    // executa atualizacao da Natureza no BD
    case 2:

        include_once '../configPatrimonio.class.php';
        $objeto = "Natureza: ".$nomNatureza;
        $patrimonio = new configPatrimonio;
        $patrimonio->setaVariaveisNatureza($codNatureza,$nomNatureza);

        // verifica se ja existe uma natureza cadastrada com o nome informado para a natureza selecionada
        if (comparaValor("nom_natureza", $nomNatureza,"patrimonio.natureza", "and cod_natureza <> $codNatureza",1)) {

            // se nao existe natureza cadastrada com o nome informado...
            // executa update no banco
            if ($patrimonio->updateNatureza()) {
                include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $objeto);
                $audicao->insereAuditoria();

                echo '
                    <script type="text/javascript">
                        alertaAviso("'.$objeto.'","alterar","aviso","'.Sessao::getId().'");
                        window.location = "alteraNatureza.php?'.Sessao::getId().'";
                    </script>';

            // se ocorreu erro na opecao de update exibe msg de erro
            } else {
                echo '
                    <script type="text/javascript">
                        alertaAviso("'.$objeto.'","n_alterar","aviso","'.Sessao::getId().'");
                        window.location = "alteraNatureza.php?'.Sessao::getId().'";
                    </script>';
            }

        // se existe natureza cadastrada com o nome informado, nao faz update e exibe msg de aviso
        } else {
            echo '
                <script type="text/javascript">
                    alertaAviso("A Natureza '.$nomNatureza.' já existe","unica","erro","'.Sessao::getId().'");
                    mudaTelaPrincipal("alteraNatureza.php?'.Sessao::getId().'&nomNatureza='.$nomNatureza.'&codNatureza='.$codNatureza.'&ctrl=1");
                </script>';
        }

    break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
