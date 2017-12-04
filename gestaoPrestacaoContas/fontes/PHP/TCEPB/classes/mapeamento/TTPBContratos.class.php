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
    * Data de Criação: 30/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.2  2007/04/23 15:21:30  rodrigo_sr
uc-06.03.00

Revision 1.1  2007/03/22 00:33:09  cleisson
novos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBContratos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBContratos()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT contrato.num_contrato||contrato.exercicio as num_contrato              \n";
    $stSql .= "      , to_char(contrato.dt_assinatura,'ddmmyyyy') as data_assinatura          \n";
    $stSql .= "      , to_char(contrato.vencimento,'ddmmyyyy') as prazo_vigencia              \n";
    $stSql .= "      , ( coalesce(( SELECT cpf                                                \n";
    $stSql .= "                       FROM sw_cgm_pessoa_fisica                               \n";
    $stSql .= "                      WHERE sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm ), '' ) \n";
    $stSql .= "         ||                                                                    \n";
    $stSql .= "          coalesce(( SELECT cnpj                                               \n";
    $stSql .= "                       FROM sw_cgm_pessoa_juridica                             \n";
    $stSql .= "                      WHERE sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm ), '' ) \n";
    $stSql .= "        ) as cpf_cnpj                                                          \n";
    $stSql .= "      , contrato_licitacao.cod_licitacao||contrato.exercicio as num_licitacao  \n";
    $stSql .= "      , tcepb.fn_depara_modalidade_licitacao(contrato_licitacao.cod_modalidade) as modalidade                        \n";
    $stSql .= "      , lpad(trim(translate(to_char(contrato.valor_contratado,'999999990.99' ),',.', '.,')),16,'0') as valor_total \n";
    $stSql .= "      , objeto.descricao as observacao                                         \n";
    $stSql .= "   FROM licitacao.contrato                                                     \n";
    $stSql .= "   JOIN sw_cgm                                                                 \n";
    $stSql .= "     ON sw_cgm.numcgm = contrato.cgm_contratado                                \n";
    $stSql .= "   JOIN licitacao.contrato_licitacao                                           \n";
    $stSql .= "     ON contrato_licitacao.num_contrato = contrato.num_contrato                \n";
    $stSql .= "    AND contrato_licitacao.cod_entidade = contrato.cod_entidade                \n";
    $stSql .= "    AND contrato_licitacao.exercicio = contrato.exercicio                      \n";
    $stSql .= "   JOIN licitacao.licitacao                                                    \n";
    $stSql .= "     ON licitacao.cod_licitacao  = contrato_licitacao.cod_licitacao            \n";
    $stSql .= "    AND licitacao.cod_modalidade = contrato_licitacao.cod_modalidade           \n";
    $stSql .= "    AND licitacao.cod_entidade   = contrato_licitacao.cod_entidade             \n";
    $stSql .= "    AND licitacao.exercicio      = contrato_licitacao.exercicio                \n";
    $stSql .= "   JOIN compras.objeto                                                         \n";
    $stSql .= "     ON objeto.cod_objeto = licitacao.cod_objeto                               \n";
    $stSql .= "  WHERE contrato.exercicio = '".$this->getDado('exercicio')."'                 \n";

    if ( $this->getDado('stEntidades') ) {
        $stSql .= "AND contrato.cod_entidade IN (".$this->getDado('stEntidades').")           \n";
    }
    if ( $this->getDado('inMes') ) {
        $stSql .= "AND to_char(contrato.dt_assinatura,'mm') = '".$this->getDado('inMes')."'   \n";
    }

    return $stSql;
}
}
