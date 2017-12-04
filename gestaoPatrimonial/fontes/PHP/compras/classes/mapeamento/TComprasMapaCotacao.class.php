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
    * Classe de mapeamento da tabela compras.mapa
    * Data de Criação: 22/11/2006

    * @author Analista: Gelson
    * @author Desenvolvedor: Lucas Stephanou

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.04.05
                    uc-03.-5.26

    $Id: TComprasMapaCotacao.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TComprasMapaCotacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TComprasMapaCotacao()
    {
        parent::Persistente();
        $this->setTabela("compras.mapa_cotacao");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_cotacao, cod_mapa, exercicio_cotacao, exercicio_mapa');

        $this->AddCampo( 'cod_cotacao'        ,'integer'  ,true, '' ,true  ,'TComprasCotacao');
        $this->AddCampo( 'cod_mapa'           ,'integer'  ,true, '' ,true  ,'TComprasMapa'   );
        $this->AddCampo( 'exercicio_cotacao'  ,'char'     ,true, '4',true  ,'TComprasCotacao', 'exercicio' );
        $this->AddCampo( 'exercicio_mapa'     ,'char'     ,true, '4',true  ,'TComprasMapa'   , 'exercicio' );
    }

function recuperaUltimaCotacaoMapa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaUltimaCotacaoMapa().$stFiltro.$stOrdem;
    //$this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaUltimaCotacaoMapa()
{
    $stSql  = 'SELECT MAX(mapa_cotacao.cod_cotacao) AS cod_cotacao FROM compras.mapa_cotacao
            WHERE 1=1';

    return $stSql;
}

//inclusão para ser usada no OC onde não deve demonstrar debug
function inclusaoNoDebug($boTransacao = '')
{
    $obErro     = new Erro;
    $obConexao  = new Transacao;//Conexao;
    //$this->setAuditoria( new TAuditoria() );

    if ( !$obErro->ocorreu() ) {
        $stSql = $this->montaInclusao( $boTransacao, $arBlob, $obConexao );
        // $this->setDebug( 'inclusao' );
        if ($arBlob["qtd_blob"]) {
            $boTranFalse = false;
            if ( !Sessao::getTrataExcecao() && !$boTransacao) {
                $boTransacao = true;
                $boTranFalse = true;
            }

            $obErro = $obConexao->__executaDML( $stSql, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                for ($inX=0; $inX<$arBlob["qtd_blob"]; $inX++) {
                    $obConexao->gravaBlob( $arBlob["blob_oid"][$inX], $arBlob["blob"][$inX] );
                }

                if ($boTranFalse) {
                    $obConexao->fechaTransacao( $boTranFalse, $boTransacao, $obErro );
                }
            }
        }else
            $obErro = $obConexao->__executaDML( $stSql, $boTransacao );
    }

    return $obErro;
}

}
