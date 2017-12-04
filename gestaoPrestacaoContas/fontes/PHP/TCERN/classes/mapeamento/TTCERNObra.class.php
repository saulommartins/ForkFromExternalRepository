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
    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCERNObra extends Persistente
{

function TTCERNObra()
{
    parent::Persistente();
    $this->setTabela('tcern.obra');

    $this->setCampoCod('num_obra');
    $this->setComplementoChave('cod_entidade, exercicio');

    $this->AddCampo('cod_entidade'        , 'integer', true , ''    , false, false);
    $this->AddCampo('exercicio'           , 'varchar', true , '4'   , false, true);
    $this->AddCampo('num_obra'            , 'integer', true , ''    , false, true);
    $this->AddCampo('obra'                , 'varchar', true , '150' , false, false);
    $this->AddCampo('objetivo'            , 'varchar', true , '50'  , false, false);
    $this->AddCampo('localizacao'         , 'varchar', true , '50'  , false, true);
    $this->AddCampo('cod_cidade'          , 'varchar', true , '4'   , false, true);
    $this->AddCampo('cod_recurso_1'       , 'integer', false, ''    , false, false);
    $this->AddCampo('cod_recurso_2'       , 'integer', false, ''    , false, false);
    $this->AddCampo('cod_recurso_3'       , 'integer', false, ''    , false, false);
    $this->AddCampo('valor_recurso_1'     , 'numeric', false, '14,2', false, false);
    $this->AddCampo('valor_recurso_2'     , 'numeric', false, '14,2', false, false);
    $this->AddCampo('valor_recurso_3'     , 'numeric', false, '14,2', false, false);
    $this->AddCampo('valor_orcamento_base', 'numeric', true , '14,2', false, false);
    $this->AddCampo('projeto_existente'   , 'varchar', true , '255' , false, false);
    $this->AddCampo('observacao'          , 'varchar', true , '100' , false, false);
    $this->AddCampo('latitude'            , 'numeric', true , '14,2', false, false);
    $this->AddCampo('longitude'           , 'numeric', true , '14,2', false, false);
    $this->AddCampo('rdc'                 , 'integer', true , ''    , false, false);
}

function recuperaObraEntidade(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaObraEntidade().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaObraEntidade()
{
    $stSql .= " SELECT   'N° Obra: ' || obra.num_obra || ' - Entidade: ' || sw_cgm.nom_cgm || ' - Exercício: ' || obra.exercicio AS obra,
                                        obra.num_obra || '§' || entidade.cod_entidade || '§' || obra.exercicio AS num_obra
                  FROM tcern.obra
            INNER JOIN orcamento.entidade
                    ON entidade.cod_entidade = obra.cod_entidade
                   AND entidade.exercicio    = obra.exercicio
            INNER JOIN sw_cgm
                    ON sw_cgm.numcgm = entidade.numcgm ";

    return $stSql;
}

}
