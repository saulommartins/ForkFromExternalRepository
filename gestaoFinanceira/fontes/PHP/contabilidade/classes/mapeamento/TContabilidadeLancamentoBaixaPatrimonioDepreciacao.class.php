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
  * Mapeamento Função baixa de bens
  * Data de Criação: 26/02/2016

  * @author Analista: Gelson Wolvowski Gonçalves 
  * @author Desenvolvedor: Arthur Cruz
  * @ignore
  *
  * $Id: $
  * $Revision: $
  * $Author: $
  * $Date: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TContabilidadeLancamentoBaixaPatrimonioDepreciacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela('contabilidade.fn_insere_lancamentos_baixa_patrimonio_depreciacao');
        $this->setCampoCod('id');
        $this->setComplementoChave('id, exercicio, cod_entidade, tipo, cod_lote, sequencia, cod_bem');

        $this->AddCampo('id'          , 'integer'  , true, ''  , true , false);
        $this->AddCampo('exercicio'   , 'char'     , true, '04', false, true );
        $this->AddCampo('cod_entidade', 'integer'  , true, ''  , false, true );
        $this->AddCampo('tipo'        , 'char'     , true, '01', false, true );
        $this->AddCampo('cod_lote'    , 'integer'  , true, ''  , false, true );
        $this->AddCampo('sequencia'   , 'integer'  , true, ''  , false, true );
        $this->AddCampo('cod_bem'     , 'integer'  , true, ''  , false, true );
        $this->AddCampo('estorno'     , 'boolean'  , true, ''  , false, false);
    }

    public function insereLancamentosBaixaPatrimonioDepreciacao(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaInsereLancamentosBaixaPatrimonioDepreciacao();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaInsereLancamentosBaixaPatrimonioDepreciacao()
    {        
        $stSql  = " SELECT contabilidade.fn_insere_lancamentos_baixa_patrimonio_depreciacao (  '".$this->getDado("stExercicio")."'
                                                                                             , '".$this->getDado("stCodBem")."'
                                                                                             , ".$this->getDado("inTipoBaixa")."
                                                                                             , '".$this->getDado("stDataBaixa")."'
                                                                                             , ".$this->getDado("inCodHistorico")."
                                                                                             , '".$this->getDado("stTipo")."'
                                                                                             , ".$this->getDado("boEstorno")."
                                                                                            ); ";
        return $stSql;
    }
    
    
    public function recuperaBemBaixaDepreciacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $stGrupo = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaBemBaixaDepreciacao().$stFiltro.$stOrdem.$stGrupo;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaBemBaixaDepreciacao()
    {        
        $stSql  = "
            SELECT bem.cod_bem
                 , bem.cod_bem || '- ' || TRIM(bem.descricao) AS descricao_bem
                 , bem.vl_bem
                 , grupo_plano_analitica.cod_plano
                 , grupo_plano_analitica.cod_plano_doacao
                 , grupo_plano_analitica.cod_plano_perda_involuntaria
                 , grupo_plano_analitica.cod_plano_transferencia
                 , natureza.cod_tipo
                 , natureza.cod_natureza
                 , natureza.nom_natureza
                 , grupo.cod_grupo
                 , grupo.nom_grupo
                 , bem_comprado.cod_entidade
    
              FROM patrimonio.bem
              
        INNER JOIN patrimonio.bem_comprado
                ON bem_comprado.cod_bem = bem.cod_bem
    
        INNER JOIN patrimonio.especie
                ON especie.cod_natureza = bem.cod_natureza
               AND especie.cod_grupo    = bem.cod_grupo
               AND especie.cod_especie  = bem.cod_especie
    
        INNER JOIN patrimonio.grupo
                ON grupo.cod_natureza = especie.cod_natureza
               AND grupo.cod_grupo    = especie.cod_grupo
    
        INNER JOIN patrimonio.natureza
                ON natureza.cod_natureza = grupo.cod_natureza
    
        INNER JOIN patrimonio.grupo_plano_analitica
                ON grupo_plano_analitica.cod_grupo    = grupo.cod_grupo
               AND grupo_plano_analitica.cod_natureza = grupo.cod_natureza
               AND grupo_plano_analitica.exercicio    = '".$this->getDado("stExercicio")."'
    
        INNER JOIN patrimonio.depreciacao
                ON depreciacao.cod_bem = bem.cod_bem
    
             WHERE NOT EXISTS ( SELECT 1 
                                 FROM patrimonio.depreciacao_anulada
                                WHERE depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
                                  AND depreciacao_anulada.cod_bem         = depreciacao.cod_bem
                                  AND depreciacao_anulada.timestamp       = depreciacao.timestamp ) \n";
          return $stSql;
    }
}

?>