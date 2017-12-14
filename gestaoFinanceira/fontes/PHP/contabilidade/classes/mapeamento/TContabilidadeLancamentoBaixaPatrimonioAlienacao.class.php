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
  * Mapeamento Função baixa de bens por Alienação
  * Data de Criação: 19/04/2016

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

class TContabilidadeLancamentoBaixaPatrimonioAlienacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::__construct();
        
        $this->setTabela('contabilidade.lancamento_baixa_patrimonio_alienacao');
        $this->setCampoCod('id');
        $this->setComplementoChave('exercicio, cod_entidade, tipo, cod_lote, sequencia, cod_bem, cod_arrecadacao, exercicio_arrecadacao');

        $this->AddCampo('id'                    , 'integer'  , true, ''  , true , false);
        $this->AddCampo('timestamp'             , 'timestamp', true, ''  , false, true );
        $this->AddCampo('exercicio'             , 'char'     , true, '04', false, true );
        $this->AddCampo('cod_entidade'          , 'integer'  , true, ''  , false, true );
        $this->AddCampo('tipo'                  , 'char'     , true, '01', false, true );
        $this->AddCampo('cod_lote'              , 'integer'  , true, ''  , false, true );
        $this->AddCampo('sequencia'             , 'integer'  , true, ''  , false, true );
        $this->AddCampo('cod_bem'               , 'integer'  , true, ''  , false, true );
        $this->AddCampo('cod_arrecadacao'       , 'integer'  , true, ''  , false, true );
        $this->AddCampo('exercicio_arrecadacao' , 'char'     , true, '04', false, true );
        $this->AddCampo('timestamp_arrecadacao' , 'timestamp', true, ''  , false, true );
        $this->AddCampo('estorno'               , 'boolean'  , true, ''  , false, false);
    }

    public function insereLancamentosBaixaPatrimonioAlienacao(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet();

        $stSql = $this->montaInsereLancamentosBaixaPatrimonioAlienacao();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaInsereLancamentosBaixaPatrimonioAlienacao()
    {        
        $stSql  = " SELECT contabilidade.fn_insere_lancamentos_baixa_patrimonio_alienacao (  ".$this->getDado("stCodBem")."
                                                                                           , ".$this->getDado("inCodPlano")."
                                                                                           , ".$this->getDado("inCodEntidade")."
                                                                                           , '".$this->getDado("stExercicio")."'
                                                                                           , '".$this->getDado("stDataBaixa")."'
                                                                                           , ".$this->getDado("nuValorAlienacao")."
                                                                                           , ".$this->getDado("inCodArrecadacao")."
                                                                                           , ".$this->getDado("inCodRecurso")."
                                                                                           , '".$this->getDado("stExercicioArrecadacao")."'
                                                                                           , '".$this->getDado("stTimestampArrecadacao")."'
                                                                                           , ".$this->getDado("inCodHistorico")."
                                                                                           , '".$this->getDado("stTipo")."'
                                                                                           , ".$this->getDado("boEstorno")."
                                                                                          ); ";
        return $stSql;
    }
    
}

?>