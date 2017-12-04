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
Revision 1.3  2007/04/26 20:21:11  bruce
*** empty log message ***

Revision 1.2  2007/04/23 15:28:16  rodrigo_sr
uc-06.03.00

Revision 1.1  2007/03/16 01:00:26  cleisson
novos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBParticipantes extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBParticipantes()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos()
{
    $stSql  = " select cotacao_licitacao.cod_licitacao || cotacao_licitacao.exercicio_licitacao as num_licitacao
                     ,  tcepb.fn_depara_modalidade_licitacao( cotacao_licitacao.cod_modalidade ) as modalidade
                     , ( coalesce (  ( select cpf
                                               from sw_cgm_pessoa_fisica
                                              where sw_cgm_pessoa_fisica.numcgm =sw_cgm.numcgm )   , '' )
                        ||
                         coalesce (   ( select cnpj
                                               from sw_cgm_pessoa_juridica
                                              where sw_cgm_pessoa_juridica.numcgm =sw_cgm.numcgm ) , '' )
                        ) as cpf_cnpj

                  from licitacao.cotacao_licitacao
                  join compras.cotacao_fornecedor_item
                    on ( cotacao_licitacao.cod_item          = cotacao_fornecedor_item.cod_item
                   and   cotacao_licitacao.cgm_fornecedor    = cotacao_fornecedor_item.cgm_fornecedor
                   and   cotacao_licitacao.cod_cotacao       = cotacao_fornecedor_item.cod_cotacao
                   and   cotacao_licitacao.exercicio_cotacao = cotacao_fornecedor_item.exercicio
                   and   cotacao_licitacao.lote              = cotacao_fornecedor_item.lote          )
                  join sw_cgm
                    on ( sw_cgm.numcgm = cotacao_fornecedor_item.cgm_fornecedor )

               ";

    if ( $this->getDado('exercicio') ) {
        $stSql .= " where cotacao_licitacao.exercicio_licitacao = '".$this->getDado('exercicio')."'       \n";
    }
    if ( $this->getDado('stEntidades') ) {
        $stSql .= " AND   cotacao_licitacao.cod_entidade in (".$this->getDado('stEntidades').") \n";
    }
    if ( $this->getDado('inMes') ) {
        $stSql .= " AND     to_char( cotacao_fornecedor_item.timestamp,'mm') = '".$this->getDado('inMes')."'  \n";
    }

    $stSql .= "
                group by num_licitacao
                      ,cotacao_licitacao.cod_modalidade
                    , cpf_cnpj
              ";

    return $stSql;
}
}
