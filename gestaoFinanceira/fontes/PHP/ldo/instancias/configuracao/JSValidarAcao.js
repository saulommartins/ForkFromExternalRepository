<script type="text/javascript">
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
</script>
<?php
/**
 * javascript para validacao de acoes
 *
 * @category    Urbem
 * @package     LDO
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */
?>
<script type="text/javascript">

/**
 * Responsável por adaptar o que é necessário para o funcionamento do programa após que todos elementos DOMs estiverem carregados
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>'
 * @returns void
 */
jq(document).ready(function() {
    LiberaFrames(true, true);
    
    if (jq('form').attr('action') != undefined) {
        var arForm = jq('form').attr('action').split('?');
        
        //jq('#Ok').removeAttr('onClick');
        jq('#Ok').click(function() {
            if (Valida()) {
                document.getElementById('frm').submit();;
                BloqueiaFrames(true, false);
            } else {
                LiberaFrames(true, true);
            }
        });
        
        //JS para o FM
        if (arForm[0] == 'PRValidarAcao.php') {
            
            if (jq('#stAcao').val() == 'excluir') {
                jq('#Ok').css('display', 'none');
            }
            
            // Atribui um alinhamento a direita para todos span que tiverem na tabela de recursos
            // Além de verificar os span de totoal e de metas disponivel para deixa-los em negrito
            jq('span', jq('#obTblRecursos tbody:last')).each(function() {
                jq(this).css('text-align', 'right');
                
                if (jq(this).attr('id').indexOf('Total') > -1 || jq(this).attr('id').indexOf('Disp') > -1) {
                    jq(this).css('font-weight', 'bold');
                }
            });
            
            // Adiciona um evento no OnChange de cada checkbox da listagem de recursos
            // Ele libera os campos de meta e valor da listagem de label para input, além de alinhar a direita o texto dos inputs
            var inCodRecurso;
            jq(':checkbox', jq('#obTblRecursos tbody:last')).each(function() {
                jq(this).change(function() {
                    inCodRecurso = jq(this).attr('id').replace('chkValidar_', '');
                    setLabel('flMeta_' + inCodRecurso, jq(this).prop('checked'));
                    setLabel('flValor_' + inCodRecurso, jq(this).prop('checked'));
                    jq('#flMeta_' + inCodRecurso).css('text-align', 'right');
                    jq('#flValor_' + inCodRecurso).css('text-align', 'right');
                    // Se um usuário clicar no check deixando ele marcado, o input deve assumir o valor inicial.
                    // Isso serve para quando o usuário marca o check (liberando os inputs), trocam os valores deles e depois desmarcam o checkbox
                    // (com isso volta a ficar aparecendo o label, com o valor inicial), se o usuário marcar novamente o checkbox, deve aparecer o
                    // valor inicial, ou seja, o mesmo do label, e não o ultimo colocado no input
                    if (jq(this).prop('checked') == true) {
                        jq('#flMeta_' + inCodRecurso).val(jq('#flMeta_'+inCodRecurso+'_label').html());
                        jq('#flValor_' + inCodRecurso).val(jq('#flValor_'+inCodRecurso+'_label').html());
                    } else {
                        calculaTotal(this, true);
                    }
                });
            });
            
            
            // Altera os names de cada input para que sejam gravados como arrays. Assim teremos um array de valores e metas onde sua chave
            // é o código do recurso referente aquela linha
            // Além de fazer atribuir uma verificação no OnBlur do campo de meta para que verifique que o valor colocado naquele campo não seja
            // maior que o valor da meta disponivel
            var arId;
            var stNovoName;
            jq(':input', jq('#obTblRecursos tbody:last')).each(function() {
                // flMeta_1 (descicao - cod_recurso)
                arId = jq(this).attr('id').split('_');
                stNovoName = arId[0]+'['+arId[1]+']';
                jq(this).attr('name', stNovoName);
                
                if (jq(this).attr('id').indexOf('flMeta_') > -1) {
                    jq(this).blur(function() {
                        if (jq(this).val() != '') {
                            stDisponivel = jq(this).attr('id').replace('flMeta', 'flMetaDisponivel');
                            if (formataValor(jq(this).val()) > formataValor(jq('#'+stDisponivel).val())) {
                                jq(this).val('');
                                alertaAviso('O valor da meta não pode ser maior que o valor da meta disponível para o recurso.', '', 'aviso', '');
                            }
                        }
                    });
                }
            });
            
            
            
        // JS para o FL
        } else if (arForm[0] == 'LSValidarAcao.php') {
            //jq('#inCodPPATxt').removeAttr('onBlur');
            jq('#inCodPPATxt').blur( function() {
                jq.post('OCValidarAcao.php', {'stCtrl':'preencheLDO', 'inCodPPA':this.value}, '', 'script');
            });
            
            if (jq('#inCodPPATxt').val() != '') {
                jq('#inCodPPATxt').trigger('blur');
            }
        }
    // JS para o LS
    } else {
        jq('td[class=\'botao\'] > a' , jq('#tblListaAcoes')).each( function() {
            this.href = this.href.replace('PRValidarAcao', 'FMValidarAcao');
        });
    }
});

function formataValor(stValor)
{
    return parseFloat(stValor.replace(/\./g, '').replace(',', '.'));
}

function excluirRecurso(inCodRecursoFormatado, stRecurso, inCodRecurso, stExercicioRecurso)
{
    
    jq('#inCodRecurso').val(inCodRecurso);
    jq('#stExercicioRecurso').val(stExercicioRecurso);
    confirmPopUp( 'Exclusão'
                , 'Deseja realmente excluir a validação do recurso '+inCodRecursoFormatado+' - '+stRecurso+'?'
                , 'document.getElementById(\'frm\').submit();'
                );
}

</script>
