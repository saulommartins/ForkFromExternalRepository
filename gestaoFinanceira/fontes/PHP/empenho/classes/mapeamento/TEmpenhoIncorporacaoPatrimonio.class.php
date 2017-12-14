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
    * Classe de mapeamento da tabela empenho.incorporacao_patrimonio
    * Data de Criação: 26/10/2007

    * @author Analista: Anderson Konze
    * @author Desenvolvedor: Leopoldo Braga Barreiro

    $Id: TEmpenhoIncorporacaoPatrimonio.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.03.35

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

// Baseado em TEmpenhoItemPrestacaoContas.class.php

class TEmpenhoIncorporacaoPatrimonio extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
  public function TEmpenhoIncorporacaoPatrimonio()
  {
    parent::Persistente();
    $this->setTabela("empenho.incorporacao_patrimonio");

    $this->setCampoCod( null );
    $this->setComplementoChave('cod_nota, cod_entidade, exercicio');
    /* Retirado de Persistente.class.php
        function AddCampo(	1) $stNome,
                                            2) $stTipo,
                                            3) $boRequerido='',
                                            4) $nrTamanho='',
                                            5) $boPrimaryKey='',
                                            6) $stForeignKey='',
                                            7) $stCampoForeignKey='',
                                            8) $stConteudo='' ) 	*/
    $this->AddCampo( 'cod_nota', 'integer', true, '', true, false );
    $this->AddCampo( 'cod_entidade', 'integer', true  , '', true, false );
    $this->AddCampo( 'exercicio', 'char', true, '4', true, false );
    $this->AddCampo( 'cod_plano_credito', 'integer', true, '', false, true, 'TContabilidadePlanoAnalitica', 'cod_plano' );
    $this->AddCampo( 'cod_plano_debito', 'integer', true, '', false, true, 'TContabilidadePlanoAnalitica', 'cod_plano' );
  }

/*
  public function recuperaListagemPrestacao(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaRecuperaListagemPrestacao().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
  }

  public function montaRecuperaListagemPrestacao()
  {
    $stSql = "SELECT prestacao_contas.cod_empenho                                                   \n";
    $stSql.= "      ,prestacao_contas.cod_entidade                                                  \n";
    $stSql.= "      ,prestacao_contas.exercicio                                                     \n";
    $stSql.= "      ,TO_CHAR(prestacao_contas.data,'dd/mm/yyyy') AS data                            \n";
    $stSql.= "      ,eipc.num_item                                                                  \n";
    $stSql.= "      ,eipc.cod_documento                                                             \n";
    $stSql.= "      ,TO_CHAR(eipc.data_item,'dd/mm/yyyy') AS data_item                              \n";
    $stSql.= "      ,eipc.num_documento                                                             \n";
    $stSql.= "      ,eipc.credor                                                                    \n";
    $stSql.= "      ,eipc.justificativa                                                             \n";
    $stSql.= "      ,eipc.conta_contrapartida                                                       \n";
    $stSql.= "      ,eipc.exercicio_conta                                                           \n";
    $stSql.= "      ,eipc.valor_item                                                                \n";
    $stSql.= "  FROM empenho.prestacao_contas                                                       \n";
    $stSql.= "      ,empenho.item_prestacao_contas as eipc                                          \n";
    $stSql.= " WHERE prestacao_contas.cod_entidade = eipc.cod_entidade                              \n";
    $stSql.= "   AND prestacao_contas.cod_empenho  = eipc.cod_empenho                               \n";
    $stSql.= "   AND prestacao_contas.exercicio    = eipc.exercicio                                 \n";
    $stSql.= "   AND NOT EXISTS ( SELECT num_item                                                   \n";
    $stSql.= "                    FROM empenho.item_prestacao_contas_anulado                        \n";
    $stSql.= "                    WHERE exercicio = eipc.exercicio                                  \n";
    $stSql.= "                          AND cod_empenho  = eipc.cod_empenho                         \n";
    $stSql.= "                          AND cod_entidade = eipc.cod_entidade                        \n";
    $stSql.= "                          AND num_item     = eipc.num_item                            \n";
    $stSql.= "                    )                                                                 \n";
    $stSql.= "   AND prestacao_contas.cod_entidade = ".$this->getDado('cod_entidade')."             \n";
    $stSql.= "   AND prestacao_contas.cod_empenho  = ".$this->getDado('cod_empenho')."              \n";
    $stSql.= "   AND prestacao_contas.exercicio    = ".$this->getDado('exercicio')."                \n";

    return $stSql;
  }

  public function recuperaValorPrestado(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaRecuperaValorPrestado().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
  }

  public function montaRecuperaValorPrestado()
  {
    $stSql .= " SELECT                                                                                   \n";
    $stSql .= "     empenho.fn_consultar_valor_prestado_nao_anulado( '".$this->getDado('exercicio')."'   \n";
    $stSql .= "                                                      ,".$this->getDado('cod_empenho')."  \n";
    $stSql .= "                                                      ,".$this->getDado('cod_entidade')." \n";
    $stSql .= "                                        ) as vl_prestado                                  \n";

    return $stSql;
  }

  public function recuperaValorPrestar(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaRecuperaValorPrestar().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
  }

  public function montaRecuperaValorPrestar()
  {
    $stSql .= " SELECT                                                                       \n";
    $stSql .= "     empenho.fn_consultar_valor_prestar( '".$this->getDado('exercicio')."'    \n";
    $stSql .= "                                         ,".$this->getDado('cod_empenho')."   \n";
    $stSql .= "                                         ,".$this->getDado('cod_entidade')."  \n";
    $stSql .= "                                       ) as vl_prestar                        \n";

    return $stSql;
  }
*/

}
?>
