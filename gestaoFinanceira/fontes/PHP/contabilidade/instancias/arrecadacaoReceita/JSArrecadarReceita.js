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
    * Arquivo JavaScript - Arrecadar Receita
    * Data de Criação   : 25/02/2005


    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira 

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-06-20 10:23:22 -0300 (Qua, 20 Jun 2007) $
    
    * Casos de uso: uc-02.02.05
*/

/*
$Log$
Revision 1.5  2007/06/20 13:22:52  vitor
Bug#9412#, Bug#9413#

Revision 1.4  2007/06/18 21:01:50  vitor
#9412# #9413# 

Revision 1.3  2006/07/05 20:50:39  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado( BuscaDado ){
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function validaMes( campo , MesProcessamento ) {
    var Mes = campo.value.split('/');
    Mes[1] = parseInt( Mes[1],10 );
    if( Mes[1] != MesProcessamento ) { 
       campo.value = ''; 
       alertaAviso('@Valor inválido. (O Mês digitado não corresponde ao mês de Processamento)','form','erro','<?=Sessao::getId()?>');
    }
}

function validaValor( invalor ){

    document.frm.Ok.disabled = false;
    var valor = invalor.value;
    if (valor == ('0,00') ) {
       alertaAviso('@Valor inválido. (O Valor informado deve ser maior que zero)','form','erro','<?=Sessao::getId()?>');
       document.frm.Ok.disabled = true;
    }
}


function Limpar(){
    document.getElementById('stNomReceita').innerHTML = "&nbsp;";
    document.getElementById('stContaDebito').innerHTML = "&nbsp;";
    document.getElementById('stNomHistorico').innerHTML = "&nbsp;";
}
</script>
