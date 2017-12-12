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
    *  Arquivo de inclusão de situação de bens
    * Data de Criação   : 25/03/2003

    * @author Desenvolvedor Alessandro La-Rocca Silveira

    * @ignore

    $Revision: 23216 $
    $Name$
    $Autor: $
    $Date: 2007-06-13 10:43:53 -0300 (Qua, 13 Jun 2007) $

    * Casos de uso: uc-03.01.10
*/

/*
$Log$
Revision 1.12  2007/06/13 13:43:53  bruce
Bug #8333#

Revision 1.11  2006/10/25 13:11:28  larocca
Bug #6913#

Revision 1.10  2006/07/21 11:35:56  fernando
Inclusão do  Ajuda.

Revision 1.9  2006/07/13 14:53:57  fernando
Alteração de hints

Revision 1.8  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/interfaceHtmlLegada.class.php';
include_once '../situacao.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

setAjuda("UC-03.01.10");

if (!(isset($ctrl)))
    $ctrl = 0;

switch ($ctrl) {

    // formulario para insercao de uma nova Situacao de Bem
    case 0:
?>
        <script type="text/javascript">

            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;
                var campoaux;
/*
                campo = document.frm.nom.value.length;
                    if (campo == 0) {
                    mensagem += "@O campo Descrição da Situação é obrigatório.";
                    erro = true;
                }
*/
                campo = trim(document.frm.nom.value).length;
                  if (campo == 0) {
                   mensagem += "@O campo Descrição da Espécie é obrigatório.";
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

            function frmReset()
            {
                 //verificaCombo(document.frm.codNatureza,document.frm.codTxtNatureza);
                 document.frm.reset();
                 document.frm.nom.focus();

                 return(true);
            }

        </script>

        <form action="incluiSituacao.php?<?=Sessao::getId()?>&ctrl=1" method="POST" name="frm" target="oculto" onreset="return frmReset();"  >

        <table width="100%">

        <tr>
            <td class="alt_dados" colspan="2">Dados para a Situação do Bem</td>
        </tr>
        <tr>
            <td class="label" title="Informe a descrição da situação." width="20%">*Descrição da Situação</td>
            <td class="field" width="80%">
                <input type="text" name="nom" size="80" maxlength="60" value="">
            </td>
        </tr>
        <tr>
            <td class="field" colspan="2">
            <?php geraBotaoOk(); ?>
            </td>
        </tr>

        </table>

        </form>

<?php
    break;

    // insere nova Situacao de Bem no BD
    case 1:

        $inclui = new situacao;
        $inclui->setaVariaveis($cod, $nom);
        $inclui->geraCodigo();
        $objeto = "Situação: ".$nom;

        // verifica se existe Situacao de Bem cadastrada com o nome informado...
        if (comparaValor("nom_situacao", $nom,"patrimonio.situacao_bem","",1)) {

            // insere Situacao de bem no BD
            if ($inclui->incluiSituacao()) {

                include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
                $audicao = new auditoriaLegada;;
                $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $objeto);
                $audicao->insereAuditoria();

                echo '
                <script type="text/javascript">
                alertaAviso("'.$objeto.'","incluir","aviso","'.Sessao::getId().'");
                mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'");
                </script>';

            // se ocorreu algum erro na insercao da Situacao de Bem, exibe msg de erro
            } else {

                echo '
                <script type="text/javascript">
                alertaAviso("'.$objeto.'","n_incluir","erro","'.Sessao::getId().'");
                //mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'");
                </script>';

            }
        }

        // se possuiu uma Situacao de Bem com o nome informado,
        // nao insere a Situacao de Bem e exibe msg de erro
        else {
            echo '
            <script type="text/javascript">
            alertaAviso("A Situação '.$nom.' já existe","unica","erro","'.Sessao::getId().'");
            //mudaTelaPrincipal("incluiSituacao.php?'.Sessao::getId().'&nom='.$nom.'");
            </script>';
        }
}
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
