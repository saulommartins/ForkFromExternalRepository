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
    * Insere novas Naturezas no sistema de PATRIMÔNIO
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Leonardo Tremper
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 22597 $
    $Name$
    $Autor: $
    $Date: 2007-05-15 17:12:37 -0300 (Ter, 15 Mai 2007) $

    * Casos de uso: uc-03.01.03
*/

/*
$Log$
Revision 1.18  2007/05/15 20:12:37  leandro.zis
Bug #8332#

Revision 1.17  2006/07/21 11:35:56  fernando
Inclusão do  Ajuda.

Revision 1.16  2006/07/13 20:23:19  fernando
Alteração de hints

Revision 1.15  2006/07/13 13:51:21  fernando
Alteração de hints

Revision 1.14  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.13  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.03");
if (!(isset($ctrl)))
    $ctrl = 0;

switch ($ctrl) {

    // exibe formulario para insercao de Natureza
    case 0:
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
    </script>

    <form name="frm" action="incluiNatureza.php?<?=Sessao::getId()?>" onreset="document.frm.nomNatureza.focus()" method="POST">

    <table width="100%">

    <tr>
        <td colspan="2" class="alt_dados">Dados da Natureza</td>
    </tr>

    <tr>
        <td class="label" width="20%" title="Informe a descrição da natureza do bem.">*Descrição da Natureza</td>
        <td class="field" width="80%">
            <input type="text" name="nomNatureza" size="80" maxlength="80" value="<?=$nomNatureza;?>">
            <input type="hidden" name="ctrl" value="1">
        </td>
    </tr>

    <tr>
        <td class=field colspan=2>
        <?php geraBotaoOk(); ?>
        </td>
    </tr>

    </table>

    </form>
<?php
    break;

    // insere natureza no BD
    case 1:

    // verifica se ja existe alguma Natureza cadastrada com o nome informado
    if (comparaValor("nom_natureza", $nomNatureza,"patrimonio.natureza", "",1)) {
        //Se não existir nenhum igual
        include_once '../configPatrimonio.class.php';
        $objeto = "Natureza: ".$nomNatureza;
        $nId = pegaID("cod_natureza","patrimonio.natureza");
        $patrimonio = new configPatrimonio;
        $patrimonio->setaVariaveisNatureza($nId,$nomNatureza);

        if ($patrimonio->insereNatureza()) {
            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $objeto);
            $audicao->insereAuditoria();

            echo '
                <script type="text/javascript">
                    alertaAviso("'.$objeto.'","incluir","aviso","'.Sessao::getId().'");
                    window.location = "incluiNatureza.php?'.Sessao::getId().'";
                </script>';

        // se houver erro na insercao da natureza exibe msg de erro
        } else {
            echo '
                <script type="text/javascript">
                    alertaAviso("'.$objeto.'","n_incluir","erro","'.Sessao::getId().'");
                    window.location = "incluiNatureza.php?'.Sessao::getId().'";
                </script>';
        }

    //Se já existir algum registro com esse nome..
    } else {
        echo '
            <script type="text/javascript">
                alertaAviso("Já existe uma Natureza com esse nome","unica","erro","'.Sessao::getId().'");
                window.location = "incluiNatureza.php?'.Sessao::getId().'&nomNatureza='.$nomNatureza.'";
            </script>';
    }

    break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
