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
<?
/**
    * Arquivo Javascript utilizado no Formulario do Anexo2Receita
    * Data de Criação: 17/05/2005
    
    
    * @author Desenvolvedor: Cleisson da silva Barboza
    
    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
    
    * Casos de uso: uc-02.01.10
*/

/*
$Log$
Revision 1.3  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

?>
<script type="text/javascript">

function validaValor() {    
    stTipoRelatorio = document.frm.stTipoRelatorio.value;
    stDataInicial = document.frm.stDataInicial.value;
    stDataFinal = document.frm.stDataFinal.value;
    
    if ( stTipoRelatorio == "balanco" ) {
        if( ( stDataInicial == "" ) || ( stDataFinal == "" ) ){
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
        if ( validaValor() ){     
              document.frm.submit();
        }
        else {            
            mensagem += "@Para o relatório de Balanço informe a data inicial e final!";
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');             
        }            
    }
 }

</script>
                
