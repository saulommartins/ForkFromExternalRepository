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
 * Classe de mapeamento da tabela tcern.nota_fiscal
 *
 * @package SW2
 * @subpackage Mapeamento
 * @version $Id$
 * @author
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCERNNotaFiscal extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     * @author
     */
    public function TTCERNNotaFiscal()
    {
        parent::Persistente();
        $this->setTabela('tcern.nota_fiscal');

        $this->setCampoCod('cod_nota_liquidacao, cod_entidade, exercicio');
        $this->setComplementoChave('');

        $this->AddCampo('cod_nota_liquidacao'    , 'integer', true  , ''    , false, true);
        $this->AddCampo('cod_entidade'           , 'integer', true  , ''    , false, true);
        $this->AddCampo('exercicio'              , 'varchar', true  , '4'   , false, true);
        $this->AddCampo('nro_nota'               , 'varchar', true  , '12'  , false, false);
        $this->AddCampo('nro_serie'              , 'varchar', true  , '12'  , false, false);
        $this->AddCampo('data_emissao'           , 'date'   , true  , ''    , false, false);
        $this->AddCampo('cod_validacao'          , 'varchar', true  , '50'  , false, false);
        $this->AddCampo('modelo'                 , 'varchar', true  , '3'   , false, false);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = "  SELECT nota_fiscal.cod_nota_liquidacao
                         , nota_fiscal.cod_entidade
                         , nota_fiscal.exercicio
                         , nota_fiscal.nro_nota
                         , nota_fiscal.nro_serie
                         , to_char(nota_fiscal.data_emissao, 'dd/mm/yyyy') as data_emissao
                         , nota_fiscal.cod_validacao
                         , nota_fiscal.modelo
                      FROM tcern.nota_fiscal
                INNER JOIN empenho.nota_liquidacao
                        ON nota_liquidacao.cod_nota     = nota_fiscal.cod_nota_liquidacao
                       AND nota_liquidacao.cod_entidade = nota_fiscal.cod_entidade
                       AND nota_liquidacao.exercicio    = nota_fiscal.exercicio
                     WHERE NOT EXISTS (
                                    SELECT 1
                                      FROM empenho.nota_liquidacao_item
                                INNER JOIN empenho.nota_liquidacao_item_anulado
                                        ON nota_liquidacao_item.exercicio = nota_liquidacao_item_anulado.exercicio
                                       AND nota_liquidacao_item.cod_nota = nota_liquidacao_item_anulado.cod_nota
                                       AND nota_liquidacao_item.num_item = nota_liquidacao_item_anulado.num_item
                                       AND nota_liquidacao_item.exercicio_item = nota_liquidacao_item_anulado.exercicio_item
                                       AND nota_liquidacao_item.cod_pre_empenho = nota_liquidacao_item_anulado.cod_pre_empenho
                                       AND nota_liquidacao_item.cod_entidade = nota_liquidacao_item_anulado.cod_entidade
                                     WHERE nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
                                       AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                                       AND nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                                  )
        ";

        return $stSql;
    }
}
