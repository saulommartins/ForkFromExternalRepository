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
    * Arquivo que altera a situação do bem
    * Data de Criação   : 25/03/2003

    * @author Desenvolvedor Alessandro La-Rocca Silveira
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 12234 $
    $Name$
    $Autor: $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.9  2006/07/06 14:06:36  diego
Retirada tag de log com erro.

Revision 1.8  2006/07/06 12:11:27  diego

*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once '../bens.class.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

    $altera = new bens;
    $altera->codigo = $codBem;

    if (!(isset($controle)))
        $controle=0;

    switch ($controle) {
        case 0:
            include_once 'listarBens.php';
        break;

        case 1:

        if ($altera->selecionaBem()) {
            if ($altera->selecionaSituacao()) {
                $altera->selecionaHistorico();
                $altera->selecionaClassificacao();
                $slista = $altera->listaSituacao();
?>
                <script type="text/javascript">

                function Valida()
                {
                    var mensagem = "";
                    var erro = false;
                    var campo;
                    var campoaux;

                    campo = document.frm.situacao.value;
                    if (campo == "xxx") {
                        mensagem += "@Campo Situação é Obrigatório.";
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

            <form action="alteraSituacao.php?<?=Sessao::getId();?>&controle=2" method="POST" name="frm">

            <input type=hidden name=codKey value="<?=$altera->codigo;?>">

            <table width=100%>
            <tr>
                <td class=alt_dados colspan=2>Localização do Bem</td>
            </tr>
            <tr><td class="label" width="20%">Órgão</td><td class=field><?=$altera->orgao;?></td></tr>
            <tr><td class="label">Unidade</td><td class=field><?=$altera->unidade;?></td></tr>
            <tr><td class="label">Departamento</td><td class=field><?=$altera->departamento;?></td></tr>
            <tr><td class="label">Setor</td><td class=field><?=$altera->setor;?></td></tr>
            <tr><td class="label">Local</td><td class=field><?=$altera->local;?></td></tr>

            <tr><td class=alt_dados colspan=2>Informações do Bem</td></tr>
            <tr><td class="label">Número da Placa</td><td class=field><?php echo $altera->numPlaca; ?></td></tr>
            <tr><td class="label">Natureza</td><td class=field><?php echo $altera->natureza; ?></td></tr>
            <tr><td class="label">Grupo</td><td class=field><?php echo $altera->grupo; ?></td></tr>
            <tr><td class="label">Espécie</td><td class=field><?php echo $altera->especie; ?></td></tr>
            <tr><td class="label">Descrição</td><td class=field><?php echo $altera->descricao; ?></td></tr>

            <tr><td class=alt_dados colspan=2>Situação do Bem</td></tr>
            <tr><td class="label">Situação do Bem</td><td class=field><?php echo $altera->nomeSituacao; ?></td></tr>
            <tr><td class="label">*Nova Situação</td><td class=field>
                <select name="situacao">
                <option value="xxx" selected>Selecione uma Opção</option>
<?php
                while (list ($key, $val) = each ($slista)) {
                    print "<option value='$key'>$val</option>";
                }

?>
                </select>
                </td>
            </tr>
            <tr>
                <td colspan='2' class='field'>
                    <?=geraBotaoOk();?>
                </td>
            </tr>
            </table>

            </form>
<?php
            }
        }
    break;

    case 2:
        $altera->codSit = $situacao;
        $altera->codigo = $codKey;

        if ($altera->alteraSituacao()) {
        echo'
                <script type="text/javascript">
                alertaAviso("Situação","alterar","aviso", "'.Sessao::getId().'");
                mudaTelaPrincipal("alteraSituacao.php?'.Sessao::getId().'");
                </script>';

            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';

            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $cod); //registra os passos no auditoria
            $audicao->insereAuditoria();

        } else {
            echo'
                <script type="text/javascript">
                alertaAviso("Situação","n_alterar","erro", "'.Sessao::getId().'");
                mudaTelaPrincipal("alteraSituacao.php?'.Sessao::getId().'");
                </script>';
        }
    break;

    }
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
