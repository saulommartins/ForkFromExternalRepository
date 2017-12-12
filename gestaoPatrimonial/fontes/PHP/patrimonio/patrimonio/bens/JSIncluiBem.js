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
    * Página de 
    * Data de criação : 03/06/2005
    
    
    * @author Analista: 
    * @author Programador: Fernando Zank Correa Evangelista 
    
    $Revision: 22600 $
    $Name$
    $Author: leandro.zis $
    $Date: 2007-05-15 17:24:18 -0300 (Ter, 15 Mai 2007) $
    
    * Casos de uso: uc-03.01.06
**/

/*
$Log$
Revision 1.9  2007/05/15 20:24:18  leandro.zis
Bug #8334#

Revision 1.8  2006/07/06 14:06:36  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:11:27  diego


*/
?>
<script type="text/javascript">

function Limpar(){
    
    document.getElementById('fornecedor').innerHTML.reset;
    document.getElementById('sFornecedor').innerHTML = "" ;
    document.frm.codNatureza.focus();        

}

function validaGarantia(dt1,dt2){
    var hoje = new Date();
    var ano = hoje.getYear();
    if(ano >= 50 && ano <= 99)
        ano = 1900 + ano
    else
        ano = 2000 + ano;
    
    var pos1 = dt1.indexOf("/",0)
    var dd = dt1.substring(0,pos1)
    pos2 = dt1.indexOf("/", pos1 + 1)
    var mm = dt1.substring(pos1 + 1,pos2)
    var aa = dt1.substring(pos2 + 1,10)
    
        if(aa.length < 4)
            if(ano > 1999)
            aa = (2000 + parseInt(aa,10))
        else
            aa = (1900 + parseInt(aa,10));
    var data1 = new Date(parseInt(aa,10),parseInt(mm,10) - 1, parseInt(dd,10));
    var pos1 = dt2.indexOf("/",0)
    var dd = dt2.substring(0,pos1)
    pos2 = dt2.indexOf("/", pos1 + 1)
    var mm = dt2.substring(pos1 + 1,pos2)
    var aa = dt2.substring(pos2 + 1,10)
    if(aa.length < 4)
        if(ano > 80 && ano <= 99)
            aa = (1900 + parseInt(aa,10))
        else
            aa = (2000 + parseInt(aa,10));
    var data2 = new Date(parseInt(aa,10),parseInt(mm,10) - 1,parseInt(dd,10));
    
    if(data1 > data2){
        return true;
    } else{
        if (document.frm.dataGarantia.value != ''){
            document.frm.dataGarantia.focus();
            document.frm.dataGarantia.value='';
            return false;
        }else {
            return true;
        }
    }
}

</script>

