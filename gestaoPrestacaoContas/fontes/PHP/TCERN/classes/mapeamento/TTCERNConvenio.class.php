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
    * Extensão da Classe de mapeamento
    * Data de Criação: 2/02/2012

    * @author Desenvolvedor: Carlos Adriano

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCERNConvenio extends Persistente
{

/*
 * Método Construtor
 *
 * @return void
 * @author
 */
function TTCERNConvenio()
{
    parent::Persistente();
    $this->setTabela('tcern.convenio');

    $this->setCampoCod('num_convenio');
    $this->setComplementoChave('exercicio, cod_entidade');

    $this->AddCampo('num_convenio'       , 'integer' , true  , ''     , false, true);
    $this->AddCampo('cod_entidade'       , 'integer' , true  , ''     , false, true);
    $this->AddCampo('exercicio'          , 'varchar' , true  , '4'    , false, true);
    $this->AddCampo('cod_processo'       , 'integer' , true  , ''     , false, true);
    $this->AddCampo('exercicio_processo' , 'varchar' , true  , '4'    , false, true);
    $this->AddCampo('numcgm_recebedor'   , 'integer' , true  , ''     , false, false);
    $this->AddCampo('cod_objeto'         , 'integer' , true  , ''     , false, false);
    $this->AddCampo('cod_recurso_1'      , 'integer' , true  , ''     , false, false);
    $this->AddCampo('cod_recurso_2'      , 'integer' , true  , ''     , false, false);
    $this->AddCampo('cod_recurso_3'      , 'integer' , true  , ''     , false, false);
    $this->AddCampo('valor_recurso_1'    , 'numeric' , true  , '14,2' , false, false);
    $this->AddCampo('valor_recurso_2'    , 'numeric' , true  , '14,2' , false, false);
    $this->AddCampo('valor_recurso_3'    , 'numeric' , true  , '14,2' , false, false);
    $this->AddCampo('dt_inicio_vigencia' , 'date'    , true  , ''     , false, false);
    $this->AddCampo('dt_termino_vigencia', 'date'    , true  , ''     , false, false);
    $this->AddCampo('dt_assinatura'      , 'date'    , true  , ''     , false, false);
    $this->AddCampo('dt_publicacao'      , 'date'    , true  , ''     , false, false);
}

function recuperaConvenio(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaConvenio().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConvenio()
{
    $stSql .= " SELECT *
                  FROM tcern.convenio                                      \n
                 WHERE cod_entidade  = ".$this->getDado('cod_entidade')."  \n
                   AND exercicio     = '".$this->getDado('exercicio')."'   \n
                   AND num_convenio  = ".$this->getDado('num_convenio')."  \n";

    return $stSql;
}

function recuperaConvenioEntidade(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaConvenioEntidade().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConvenioEntidade()
{
    $stSql .= " SELECT   'N° Convênio: ' || convenio.num_convenio || ' - Entidade: ' || sw_cgm.nom_cgm || ' - Exercício: ' || convenio.exercicio AS convenio
                       , convenio.num_convenio || '§' || entidade.cod_entidade || '§' || convenio.exercicio AS num_convenio
                  FROM tcern.convenio
            INNER JOIN orcamento.entidade
                    ON entidade.cod_entidade = convenio.cod_entidade
                   AND entidade.exercicio    = convenio.exercicio
            INNER JOIN sw_cgm
                    ON sw_cgm.numcgm = entidade.numcgm ";

    return $stSql;
}

}
