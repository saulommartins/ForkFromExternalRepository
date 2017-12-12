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
    * Arquivo JS utilizado na Reserva de Saldos
    * Data de Criação   : 04/05/2005


    * @author Analista: Diego Barbosa Victória
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
    
    * Casos de uso: uc-02.01.08  
*/

/*
$Log$
Revision 1.3  2006/07/05 20:43:33  cleisson
Adicionada tag Log aos arquivos

*/
?>

<script type="text/javascript">

function buscaDado( BuscaDado ){
    document.frm.stCtrl.value = BuscaDado;
    stAction = document.frm.action;
    stTarget = document.frm.target;
    inCodDespesa = document.frm.inCodDespesa.value;
    document.frm.action = '../relatorio/OCReservaSaldos.php?stCtrl='+BuscaDado+'&inCodDespesa='+inCodDespesa+'&<?=Sessao::getId();?>';
    document.frm.target = 'oculto';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function validaData() {    
    stDataInicial  = document.frm.stDtInicial.value;
    stDataFinal    = document.frm.stDtFinal.value;    
    stExercicioInicial = stDataInicial.substr(6,4);
    stExercicioFinal = stDataFinal.substr(6,4);
    if( ( stExercicioInicial != '' ) && ( stExercicioFinal != '' ) ){
        if ( stExercicioInicial != stExercicioFinal ) {
            return false;              
        }    
        else{
            return true;              
        }
    }    
    else{
        return true;              
    }               
}
function Salvar(){
    var mensagem   = "";
    if( Valida() ){        
        if ( validaData() ){     
              document.frm.submit();
        }
        else {
            document.frm.stDtInicial.focus();
            mensagem += "@O exercicio da data de reserva inicial deve ser igual ao da data final!";
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');             
        }            
    }
 }

</script>               
