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
    * Página JS para Manter Documento e Manter Vinculo

    * Data de Criação   : 25/07/2008


    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore



*/
?>
<script type="text/javascript">

    function preencheProxCombo(inPosicao){
        document.frm.stCtrl.value = 'preencheProxCombo';
        document.frm.target = "oculto";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        
        if(inPosicao == document.frm.inNumNiveis.value){
            montaParametrosGET('buscaDocumentos', '', true);
        } 
        
    }

    function preencheCombosAtividade(){
        document.frm.stCtrl.value = 'preencheCombosAtividade';
        document.frm.target = "oculto";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    }

    function buscaValor(tipoBusca){
        document.frm.stCtrl.value = tipoBusca;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    }

    function incluirAtividade(){
        var stTarget = document.frm.target;
        var stAction = document.frm.action;
        document.frm.stCtrl.value = 'montaAtividade';
        document.frm.target = 'oculto';
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.target = stTarget;
        document.frm.action = stAction;
    }

    function limparAtividade(){

        document.frm.dtDataTermino.value = '';
        document.frm.stChaveAtividade.value = '';
        document.frm.inCodAtividade_1.value = '';
        document.frm.stPrincipal[1].checked = true;
        
        var stTarget = document.frm.target;
        var stAction = document.frm.action;
        
        document.frm.target = "oculto";
        document.frm.stCtrl.value = 'limparAtividade';
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.target = stTarget;
        document.frm.action = stAction;    
    }

    function limpar(){

        document.frm.stCtrl.value = 'limpar';
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    }

    limparCampo = function(){
        if(document.frm.inTipoFiscalizacao.value){
           document.frm.inTipoFiscalizacao.value = ''; 
           document.frm.cmbTipoFiscalizacao.options[0].selected = 'selected' ; 
        }
        if(document.getElementById('spnForm')){
            if(document.frm.cmbDocumento){
                document.frm.cmbDocumento.value = "";
            }
            document.getElementById('spnForm').innerHTML = "";
        }
    }

    function excluirDado(stControle, inId){
        var stTarget = document.frm.target;
        var stAction = document.frm.action;
        document.frm.stCtrl.value = stControle;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        document.frm.action = stAction;
        document.frm.target = stTarget;
    }

    function Cancelar () {
    <?php
         $stLink = "&pg=".$sessao->link["pg"]."&pos=".$sessao->link["pos"];
    ?>
        document.frm.target = "";
        document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
        document.frm.submit();
    }

    function incluirDocumento(){
        if (document.frm.cmbTipoFiscalizacao.value != "") {
            if(document.frm.cmbDocumento){
                if(document.frm.cmbDocumento.value != ""){
                    montaParametrosGET( 'incluirDocumento', '', true);
                } else{
                    alertaAviso('Campo de Documento Inválido!()','form','erro','<?=Sessao::getId()?>');
                }
            } else{
                alertaAviso('Campo de Documento Inválido!()','form','erro','<?=Sessao::getId()?>');
            }
        } else {
            alertaAviso('Campo Tipo Fiscalização Inválido!()','form','erro','<?=Sessao::getId()?>');
        }
    } 

    function Cancelar(){
        <?$stLink = "&pg=".$sessao->link["pg"]."&pos=".$sessao->link["pos"];?>
        document.frm.target = "telaPrincipal";
        document.frm.action = "<?=$pgList.'?'.Sessao::getId();?>";
        document.frm.submit();
    }

    function LimparFormInicio(){
        limparCampo();
        montaParametrosGET('limparSession', '', true);
        Limpar();
        limpaFormulario();
    }
</script>
