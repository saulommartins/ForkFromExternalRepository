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
/*
 * Classe de mapeamento da tabela tceam.documento
 *
 * @package SW2
 * @subpackage Mapeamento
 * @version $Id$
 * @author eduardo.schitz@cnm.org.br
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEAMDocumento extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     * @author eduardo.schitz@cnm.org.br
     */
    public function TTCEAMDocumento()
    {
        parent::Persistente();
        $this->setTabela('tceam.documento');

        $this->setCampoCod('cod_documento');

        $this->AddCampo('cod_documento'  , 'integer', true  , ''   , true , false);
        $this->AddCampo('cod_tipo'       , 'integer', true  , ''   , false, true);
        $this->AddCampo('exercicio'      , 'varchar', true  , '4'  , false, true);
        $this->AddCampo('cod_entidade'   , 'integer', true  , ''   , false, true);
        $this->AddCampo('cod_nota'       , 'integer', true  , ''   , false, true);
        $this->AddCampo('vl_comprometido', 'numeric', false, '14,2', false, false);
        $this->AddCampo('vl_total'       , 'numeric', false, '14,2', false, false);
    }

    public function recuperaRelacionamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaRelacionamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT  documento.cod_documento
                 ,  documento.cod_tipo
                 ,  tipo_documento.descricao as descricao_tipo
                 ,  documento.exercicio
                 ,  documento.cod_entidade
                 ,  documento.cod_nota
                 ,  documento.vl_comprometido
                 ,  documento.vl_total
                 ";

        if ( $this->getDado( 'cod_tipo' ) == '1' ) {
            $stSql.= ", tipo_documento_bilhete.cod_documento ";
            $stSql.= ", tipo_documento_bilhete.numero ";
            $stSql.= ", tipo_documento_bilhete.dt_emissao ";
            $stSql.= ", tipo_documento_bilhete.dt_saida ";
            $stSql.= ", tipo_documento_bilhete.hora_saida ";
            $stSql.= ", tipo_documento_bilhete.destino ";
            $stSql.= ", tipo_documento_bilhete.dt_chegada ";
            $stSql.= ", tipo_documento_bilhete.hora_chegada ";
            $stSql.= ", tipo_documento_bilhete.motivo ";

            $stSqlInner  = " INNER JOIN tceam.tipo_documento_bilhete ";
            $stSqlInner .= " ON documento.cod_documento = tipo_documento_bilhete.cod_documento ";

        } elseif ($this->getDado( 'cod_tipo' ) == '2' ) {

            $stSql.= ", tipo_documento_diaria.cod_documento ";
            $stSql.= ", tipo_documento_diaria.funcionario ";
            $stSql.= ", tipo_documento_diaria.matricula ";
            $stSql.= ", tipo_documento_diaria.dt_saida ";
            $stSql.= ", tipo_documento_diaria.hora_saida ";
            $stSql.= ", tipo_documento_diaria.destino ";
            $stSql.= ", tipo_documento_diaria.dt_retorno ";
            $stSql.= ", tipo_documento_diaria.hora_retorno ";
            $stSql.= ", tipo_documento_diaria.motivo ";
            $stSql.= ", tipo_documento_diaria.quantidade ";

            $stSqlInner  = " INNER JOIN tceam.tipo_documento_diaria ";
            $stSqlInner .= " ON documento.cod_documento = tipo_documento_diaria.cod_documento ";

        } elseif ($this->getDado( 'cod_tipo' ) == '3' ) {

            $stSql.= ", tipo_documento_diverso.cod_documento ";
            $stSql.= ", tipo_documento_diverso.numero ";
            $stSql.= ", tipo_documento_diverso.data ";
            $stSql.= ", tipo_documento_diverso.descricao ";
            $stSql.= ", tipo_documento_diverso.nome_documento ";

            $stSqlInner  = " INNER JOIN tceam.tipo_documento_diverso ";
            $stSqlInner .= " ON documento.cod_documento = tipo_documento_diverso.cod_documento ";

        } elseif ($this->getDado( 'cod_tipo' ) == '4' ) {

            $stSql.= ", tipo_documento_folha.cod_documento ";
            $stSql.= ", tipo_documento_folha.mes ";
            $stSql.= ", tipo_documento_folha.exercicio ";

            $stSqlInner  = " INNER JOIN tceam.tipo_documento_folha ";
            $stSqlInner .= " ON documento.cod_documento = tipo_documento_folha.cod_documento ";

        } elseif ($this->getDado( 'cod_tipo' ) == '5' ) {

            $stSql.= ", tipo_documento_nota.cod_documento ";
            $stSql.= ", tipo_documento_nota.numero_nota_fiscal ";
            $stSql.= ", tipo_documento_nota.numero_serie ";
            $stSql.= ", tipo_documento_nota.numero_subserie ";
            $stSql.= ", tipo_documento_nota.data ";

            $stSqlInner  = " INNER JOIN tceam.tipo_documento_nota ";
            $stSqlInner .= " ON documento.cod_documento = tipo_documento_nota.cod_documento ";

        } elseif ($this->getDado( 'cod_tipo' ) == '6' ) {

            $stSql.= ", tipo_documento_recibo.cod_documento ";
            $stSql.= ", tipo_documento_recibo.cod_tipo_recibo ";
            $stSql.= ", tipo_recibo.descricao ";
            $stSql.= ", tipo_documento_recibo.numero ";
            $stSql.= ", tipo_documento_recibo.valor ";
            $stSql.= ", tipo_documento_recibo.data ";

            $stSqlInner  = " LEFT JOIN tceam.tipo_documento_recibo ";
            $stSqlInner .= " ON documento.cod_documento = tipo_documento_recibo.cod_documento ";
            $stSqlInner .= " LEFT JOIN tceam.tipo_recibo ";
            $stSqlInner .= " ON tipo_documento_recibo.cod_tipo_recibo = tipo_recibo.cod_tipo_recibo ";

        } else {
            $stSqlInner = "";
        }

        $stSql .= "
                      FROM tceam.documento
                INNER JOIN tceam.tipo_documento
                        ON documento.cod_tipo = tipo_documento.cod_tipo
                  ";

        $stSql .= $stSqlInner;

        return $stSql;
    }

}
