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
    * Classe de Mapeamento para relatorio da LRF - LRFTCERS- Modelo7
    * Data de Criação   : 04/08/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso uc-02.05.09

    * @ignore
*/

/*
$Log$
Revision 1.6  2006/07/05 20:44:36  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FLRFTCERSModelo7 extends Persistente
{
    public function FLRFTCERSModelo7()
    {
        parent::Persistente();
        $this->setTabela('tcers.fn_rel_modelo7');

        $this->setCampoCod('');
        $this->setComplementoChave('');

        $this->AddCampo('exercicio'   ,'varchar' ,true ,'4' ,false,false);
        $this->AddCampo('cod_entidade','integer' ,true ,''  ,false,false);
        $this->AddCampo('dt_final'    ,'varchar' ,true ,'10',false,false);
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "SELECT *                                                               \n";
        $stSql .= "FROM ".$this->getTabela()."( '".$this->getDado( 'exercicio' )."'       \n";
        $stSql .= "                            ,'".$this->getDado( 'cod_entidade' )."'    \n";
        $stSql .= "                            ,'".$this->getDado( 'dt_final' )."'        \n";
        $stSql .= ") AS RETORNO ( exercicio             char(4)                           \n";
        $stSql .= "              ,cod_recurso           integer                           \n";
        $stSql .= "              ,vl_empenhado          numeric                           \n";
        $stSql .= "              ,vl_anulado            numeric                           \n";
        $stSql .= "              ,vl_liquidacao         numeric                           \n";
        $stSql .= "              ,vl_liquidacao_anulado numeric                           \n";
        $stSql .= "              ,vl_pago               numeric                           \n";
        $stSql .= "              ,vl_pago_anulado       numeric                           \n";
        $stSql .= "              ,vl_lq_ajustado        numeric                           \n";
        $stSql .= "              ,vl_n_lq_ajustado      numeric                           \n";
        $stSql .= "              ,vl_saldo              numeric                           \n";
        $stSql .= "              ,vl_saldo_ajustado     numeric                           \n";
        $stSql .= ")                                                                      \n";

        return $stSql;
    }

}
