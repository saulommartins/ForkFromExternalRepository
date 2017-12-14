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
    * Classe de mapeamento da tabela DIVIDA.PARCELA_ORIGEM
    * Data de Criação: 16/02/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATDividaParcelaOrigem.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.1  2007/02/23 18:49:01  cercato
alteracoes em funcao das mudancas no ER.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATDividaParcelaOrigem extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaParcelaOrigem()
    {
        parent::Persistente();
        $this->setTabela('divida.parcela_origem');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_parcela, cod_especie, cod_genero, cod_natureza, cod_credito, num_parcelamento');

        $this->AddCampo('cod_parcela','integer',true,'',true,true);
        $this->AddCampo('cod_especie','integer',true,'',true,true);
        $this->AddCampo('cod_genero','integer',true,'',true,true);
        $this->AddCampo('cod_natureza','integer',true,'',true,true);
        $this->AddCampo('cod_credito','integer',true,'',true,true);
        $this->AddCampo('num_parcelamento','integer',true,'',true,true);

        $this->AddCampo('valor','numeric',true,'',false,false);

    }

    public function recuperaParcelaOrigemPorIscricao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaParcelaOrigemPorIscricao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaParcelaOrigemPorIscricao()
    {
        $stSQL .= "    SELECT parcela_origem.cod_parcela                                              \n";
        $stSQL .= "         , parcela_origem.cod_especie                                              \n";
        $stSQL .= "         , parcela_origem.cod_genero                                               \n";
        $stSQL .= "         , parcela_origem.cod_natureza                                             \n";
        $stSQL .= "         , parcela_origem.cod_credito                                              \n";
        $stSQL .= "         , parcela_origem.num_parcelamento                                         \n";
        $stSQL .= "         , divida_parcelamento_ultimo.num_parcelamento AS num_parcelamento_ultimo  \n";
        $stSQL .= "         , parcela_origem.valor                                                    \n";
        $stSQL .= "      FROM divida.parcelamento                                                     \n";
        $stSQL .= "INNER JOIN (  SELECT MIN(divida_parcelamento.num_parcelamento) AS num_parcelamento \n";
        $stSQL .= "                   , divida_parcelamento.cod_inscricao                             \n";
        $stSQL .= "                   , divida_parcelamento.exercicio                                 \n";
        $stSQL .= "                FROM divida.divida_parcelamento                                    \n";
        $stSQL .= "               WHERE divida_parcelamento.cod_inscricao = ".$this->getDado('cod_inscricao')."\n";
        $stSQL .= "                 AND divida_parcelamento.exercicio = '".$this->getDado('exercicio')."'\n";
        $stSQL .= "            GROUP BY divida_parcelamento.cod_inscricao                             \n";
        $stSQL .= "                   , divida_parcelamento.exercicio) AS divida_parcelamento         \n";
        $stSQL .= "        ON parcelamento.num_parcelamento = divida_parcelamento.num_parcelamento    \n";
        $stSQL .= "INNER JOIN (  SELECT MAX(divida_parcelamento.num_parcelamento) AS num_parcelamento \n";
        $stSQL .= "                   , divida_parcelamento.cod_inscricao                             \n";
        $stSQL .= "                   , divida_parcelamento.exercicio                                 \n";
        $stSQL .= "                FROM divida.divida_parcelamento                                    \n";
        $stSQL .= "               WHERE divida_parcelamento.cod_inscricao = ".$this->getDado('cod_inscricao')."\n";
        $stSQL .= "                 AND divida_parcelamento.exercicio = '".$this->getDado('exercicio')."'\n";
        $stSQL .= "            GROUP BY divida_parcelamento.cod_inscricao                              \n";
        $stSQL .= ", divida_parcelamento.exercicio) AS divida_parcelamento_ultimo                      \n";
        $stSQL .= "        ON divida_parcelamento.cod_inscricao = divida_parcelamento_ultimo.cod_inscricao \n";
        $stSQL .= "       AND divida_parcelamento.exercicio = divida_parcelamento_ultimo.exercicio     \n";
        $stSQL .= "INNER JOIN divida.parcela_origem                                                    \n";
        $stSQL .= "        ON parcelamento.num_parcelamento = parcela_origem.num_parcelamento          \n";

        return $stSQL;

    }
}// end of class
?>
