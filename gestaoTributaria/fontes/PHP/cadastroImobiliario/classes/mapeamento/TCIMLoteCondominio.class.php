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
     * Classe de mapeamento para a tabela IMOBILIARIO.LOTE_CONDOMINIO
     * Data de Criação: 02/09/2005

     * @author Analista: Fabio Bertoldi
     * @author Desenvolvedor: Marcelo B. Paulino

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMLoteCondominio.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.14
*/

/*
$Log$
Revision 1.6  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.LOTE_CONDOMINIO
  * Data de Criação: 02/09/2005

     * @author Analista: Fabio Bertoldi
     * @author Desenvolvedor: Marcelo B. Paulino

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMLoteCondominio extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMLoteCondominio()
{
    parent::Persistente();
    $this->setTabela('imobiliario.lote_condominio');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_lote,cod_condominio');

    $this->AddCampo('cod_lote','integer',true,'',true,true);
    $this->AddCampo('cod_condominio','integer',true,'',true,true);

}

function recuperaCondominioLotesLocalizacao(&$rsRecordSet, $stFiltro, $stOrdem, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaCondominioLotesLocalizacao().$stFiltro.$stOrdem;

    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCondominioLotesLocalizacao()
{
        $stSql .= "SELECT                                                  \n";
        $stSql .= "    LO.codigo_composto,                                 \n";
        $stSql .= "    LC.cod_condominio,                                  \n";
        $stSql .= "    LC.cod_lote,                                        \n";
        $stSql .= "    LL.valor,                                           \n";
        $stSql .= "    LL.cod_localizacao,                                  \n";
        $stSql .= "     array_to_string(ARRAY(
                                SELECT inscricao_municipal FROM imobiliario.imovel_lote WHERE imovel_lote.cod_lote = LC.cod_lote), ','
                        ) as imovel \n";

        $stSql .= "FROM                                                    \n";
        $stSql .= "    IMOBILIARIO.LOTE_CONDOMINIO AS LC,                  \n";
        $stSql .= "    IMOBILIARIO.LOTE_LOCALIZACAO AS LL,                 \n";
        $stSql .= "    IMOBILIARIO.LOCALIZACAO as LO                       \n";
        $stSql .= "WHERE                                                   \n";
        $stSql .= "    LC.COD_LOTE = LL.COD_LOTE AND                       \n";
        $stSql .= "    LL.COD_LOCALIZACAO = LO.COD_LOCALIZACAO             \n";

        return $stSql;
}

}
