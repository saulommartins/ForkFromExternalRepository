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
 * Classe de mapeamento para notificao_fiscalizacao
 * Data de Criação: 28/08/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Fellipe Esteves dos Santos

 * @package URBEM
 * @subpackage Mapeamento

 * Casos de uso:
 */

/**
 * Classe de mapeamento para auto_fiscalizacao.
 */
class TFISAutoFiscalizacao extends Persistente
{
    /**
     * Método construtor
     * @access public
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela( 'fiscalizacao.auto_fiscalizacao' );

        $this->setCampoCod( 'cod_auto_fiscalizacao' );
        $this->setComplementoChave( 'cod_processo, cod_processo, cod_auto_fiscalizacao' );

        $this->addCampo( 'cod_processo', 'integer', true, '', true, true );
        $this->addCampo( 'cod_auto_fiscalizacao', 'integer', true, '', true, true );
        $this->addCampo( 'cod_fiscal', 'integer', true, '', false, false );
        $this->addCampo( 'cod_tipo_documento', 'integer', true, '', false, false );
        $this->addCampo( 'cod_documento', 'integer', true, '', false, false );
        $this->addCampo( 'dt_notificacao', 'date', true, '', false, false );
        $this->addCampo( 'observacao', 'text', true, '', false, false );
        $this->addCampo( 'timestamp', 'timestamp', false, '', false, false );
    }

    public function recuperaAutoFiscalizacao(&$rsRecordSet, $stCondicao, $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaAutoFiscalizacao($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaAutoFiscalizacao($condicao)
    {
        $stSql ="  SELECT                                                               \n";
        $stSql.="    tf.cod_tipo,                                                       \n";
        $stSql.="    tf.descricao,                                                      \n";
        $stSql.="    ainf.cod_processo,                                                 \n";
        $stSql.="    ainf.cod_auto_fiscalizacao,                                        \n";
        $stSql.="    ainf.dt_notificacao                                                \n";
        $stSql.="  FROM                                                                 \n";
        $stSql.="    fiscalizacao.auto_fiscalizacao ainf                                \n";
        $stSql.="  INNER JOIN                                                           \n";
        $stSql.="    fiscalizacao.processo_fiscal pf                                    \n";
        $stSql.="    on pf.cod_processo = ainf.cod_processo                             \n";
        $stSql.="  INNER JOIN                                                           \n";
        $stSql.="    fiscalizacao.tipo_fiscalizacao tf                                  \n";
        $stSql.="    on tf.cod_tipo = pf.cod_tipo                                       \n";
        $stSql.="  $condicao                                                            \n";

        return $stSql;
    }

}
