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
    * Classe de mapeamento da tabela
    * Data de Criação: 01/08/2006

    * @author Analista: Cleissom
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
class TSNT_RREO_AnexoI_Despesa extends Persistente
{
    public function TSNT_RREO_AnexoI_Despesa()
    {
        parent::Persistente();
        $this->setTabela('stn.fn_rreo_anexo1_despesas');

        $this->AddCampo( 'grupo'                 ,'integer', false, ''    , false, false );
        $this->AddCampo( 'nivel'                 ,'integer', false, ''    , false, false );
        $this->AddCampo( 'cod_estrutural'        ,'varchar', false, ''    , false, false );
        $this->AddCampo( 'descricao'             ,'varchar', false, ''    , false, false );
        $this->AddCampo( 'dotacao_inicial'       ,'numeric', false, ''    , false, false );
        $this->AddCampo( 'creditos_adicionais'   ,'numeric', false, ''    , false, false );
        $this->AddCampo( 'dotacao_atualizada'    ,'numeric', false, ''    , false, false );
        $this->AddCampo( 'vl_empenhado_bimestre' ,'numeric', false, ''    , false, false );
        $this->AddCampo( 'vl_empenhado_total'    ,'numeric', false, '14.2', false, false );
        $this->AddCampo( 'vl_liquidado_bimestre' ,'numeric', false, '14.2', false, false );
        $this->AddCampo( 'vl_liquidado_total'    ,'numeric', false, '14.2', false, false );
        $this->AddCampo( 'vl_pago_total'         ,'numeric', false, '14.2', false, false );
        $this->AddCampo( 'percentual'            ,'numeric', false, '14.2', false, false );
        $this->AddCampo( 'saldo_liquidar'        ,'numeric', false, '14.2', false, false );
    }

    public function montaRecuperaTodos()
    {
        $stSql .= "select * from stn.fn_rreo_anexo1_despesas( '".$this->getDado("exercicio")."', '" . $this->getDado("dt_inicial"). "' , '" . $this->getDado("dt_final"). "', '". $this->getDado('entidades')."'  ) as\n";
        $stSql .= "retorno (                                                                                          \n";
        $stSql .= "grupo                   integer ,                                                                  \n";
        $stSql .= "cod_estrutural          varchar ,                                                                  \n";
        $stSql .= "descricao               varchar ,                                                                  \n";
        $stSql .= "nivel                   integer ,                                                                  \n";
        $stSql .= "dotacao_inicial         numeric(14,2) ,                                                            \n";
        $stSql .= "creditos_adicionais     numeric(14,2) ,                                                            \n";
        $stSql .= "dotacao_atualizada      numeric(14,2) ,                                                            \n";
        $stSql .= "vl_empenhado_bimestre   numeric(14,2) ,                                                            \n";
        $stSql .= "vl_empenhado_total      numeric(14,2) ,                                                            \n";
        $stSql .= "vl_liquidado_bimestre   numeric(14,2) ,                                                            \n";
        $stSql .= "vl_liquidado_total      numeric(14,2) ,                                                            \n";
        $stSql .= "vl_pago_total           numeric(14,2) ,                                                            \n";
        $stSql .= "percentual              numeric(14,2) ,                                                            \n";
        $stSql .= "saldo_liquidar          numeric(14,2) )                                                            \n";

        return $stSql;
    }

}
