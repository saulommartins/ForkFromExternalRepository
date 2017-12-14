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
 * Página de JavaScript
 * Data de Criação   : 30/09/2014

 * @author Analista:      Eduardo Paculski Schitz
 * @author Desenvolvedor: Franver Sarmento de Moraes
 
 * @ignore 
 * $id: $
 * 
 * $Revision: 62838 $
 * $Name$
 * $Author: diogo.zarpelon $
 * $Date: 2015-06-26 10:02:49 -0300 (Fri, 26 Jun 2015) $
**/
?>
<script type="text/javascript">

function selecionaArquivos(boSelected) {
    i = 0;
    while (eval('document.frm.arArquivos[i]')) {
        eval('document.frm.arArquivos[i].selected = '+boSelected+';');
        i++;        
    }
}

function validaArquivos () {
    var mensagem = ""; 
    
    if(document.frm.inCodEntidade.value=='') {
        mensagem += "@Selecione a Entidade!"; 
    }
    
    if(document.frm.inCodCompetencia.value=='') {
        mensagem += "@Selecione a Competência!"; 
    }
    
    if ((document.frm.arArquivos)) {
        if(!eval(document.frm.arArquivos[0])) {
            mensagem += "@Selecione pelo menos um arquivo para ser gerado!";
        }
    }
    
    if(mensagem==""){
        return true;    
    }else{
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        return false;
    }
}

</script>