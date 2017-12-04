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
    
    * Casos de uso: uc-02.01.11
    
*/

/*
$Log$
Revision 1.6  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

?>
<script type="text/javascript">

function buscaValor(BuscaValor){
    document.frm.stCtrl.value = BuscaValor;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgRelatorio;?>?<?=Sessao::getId();?>';
}

function buscaValor_Filtro(BuscaValor){
    document.frm.stCtrl.value = BuscaValor;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function Cancelar(){
    document.frm.target = 'telaPrincipal';
    window.location = '<?=$pgList;?>?<?=Sessao::getId();?>&stAcao=<?=$stAcao;?>&pg=<?=$_GET["pg"]?>&pos=<?=$_GET["pos"]?>';
}

function somatorio( campo, numColunas ){
    var arSeiLa = campo.name.split("_");
    var campoTotal = "total_" + arSeiLa[2];
    var total = 0;
    for( var i = 1; i <= numColunas; i++ ){
       var campoDin =  arSeiLa[0] + '_' + i + '_' + arSeiLa[2].value;
       total = total + campoDin;
    }
    return document.frm.campoTotal.value = total;
}

function validaValor() {    
    stDemonstrarValores = document.frm.stDemonstrarValores.value;
    stDataInicial = document.frm.stDataInicial.value;
    stDataFinal = document.frm.stDataFinal.value;
    stSituacao =  document.frm.stSituacao.value;
    
    if ( stDemonstrarValores == "balanco" ) {
        if( ( stDataInicial == "" ) || ( stDataFinal == "" ) || ( stSituacao == "" ) ){
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
            mensagem += "@Para o relatório de Balanço informe a Situação e o Período!";
            
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');             
        }            
    }
 }

</script>
