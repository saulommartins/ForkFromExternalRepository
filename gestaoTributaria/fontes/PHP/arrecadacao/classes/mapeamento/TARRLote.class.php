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
    * Classe de mapeamento da tabela ARRECADACAO.LOTE
    * Data de Criação: 05/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo B. Paulino
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRLote.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.11 uc-02.04.33
*/

/*
$Log$
Revision 1.13  2007/08/01 14:33:03  hboaventura
Bug#9790#

Revision 1.12  2007/07/05 14:49:22  hboaventura
uc-02.04.33

Revision 1.11  2007/03/22 21:11:24  domluc
Correção Bug #8865#

Revision 1.10  2007/03/15 18:57:22  domluc
Caso de Uso 02.04.33

Revision 1.9  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.8  2006/09/15 10:41:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.LOTE
  * Data de Criação: 05/12/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Marcelo B. Paulino

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRLote extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRLote()
{
    parent::Persistente();
    $this->setTabela( "arrecadacao.lote" );

    $this->setCampoCod('cod_lote');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('cod_lote','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('data_lote','date',true,'',false,false);
    $this->AddCampo('numcgm','integer',true,'',false,true);
    $this->AddCampo('cod_banco','integer',true,'',false,true);
    $this->AddCampo('cod_agencia','integer',true,'',false,true);
    $this->AddCampo('automatico','boolean',true,'',false,false);
}

function montaRecuperaDetalheLoteTesouraria()
{
    $stSql = " select lote.cod_lote
                    , lote.exercicio
                    , to_char(lote.data_lote, 'dd/mm/YYYY') as data_lote
                    , arrecadacao.somaInconsistenciaLote  ( lote.cod_lote , lote.exercicio::int ) as soma_inconsistencia
                    , arrecadacao.somaPagamentosLote      ( lote.cod_lote , lote.exercicio::int ) as soma_pagamentos
                    , arrecadacao.contaInconsistenciaLote ( lote.cod_lote , lote.exercicio::int ) as conta_inconsistencia
                    , arrecadacao.contaPagamentosLote     ( lote.cod_lote , lote.exercicio::int ) as conta_pagamentos
                    , (select count(pagamento_lote.cod_lote)
                         from arrecadacao.pagamento_lote
                   inner join arrecadacao.pagamento
                           on pagamento.numeracao            = pagamento_lote.numeracao
                          and pagamento.cod_convenio         = pagamento_lote.cod_convenio
                          and pagamento.ocorrencia_pagamento = pagamento_lote.ocorrencia_pagamento
                   inner join arrecadacao.pagamento_calculo
                           on pagamento_calculo.numeracao            = pagamento.numeracao
                          and pagamento_calculo.cod_convenio         = pagamento.cod_convenio
                          and pagamento_calculo.ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                   inner join arrecadacao.calculo
                           on calculo.cod_calculo = pagamento_calculo.cod_calculo
                   inner join monetario.credito_conta_corrente
                           on credito_conta_corrente.cod_credito = calculo.cod_credito
                          and credito_conta_corrente.cod_especie = calculo.cod_especie
                          and credito_conta_corrente.cod_genero  = calculo.cod_genero
                          and credito_conta_corrente.cod_natureza= calculo.cod_natureza
                   inner join monetario.conta_corrente_convenio
                           on credito_conta_corrente.cod_conta_corrente = conta_corrente_convenio.cod_conta_corrente
                          and credito_conta_corrente.cod_agencia = conta_corrente_convenio.cod_agencia
                          and credito_conta_corrente.cod_banco = conta_corrente_convenio.cod_banco
                          and credito_conta_corrente.cod_convenio = conta_corrente_convenio.cod_convenio
                   inner join monetario.conta_corrente
                           on conta_corrente.cod_conta_corrente = conta_corrente_convenio.cod_conta_corrente
                          and conta_corrente.cod_agencia = conta_corrente_convenio.cod_agencia
                          and conta_corrente.cod_banco = conta_corrente_convenio.cod_banco
                   inner join contabilidade.plano_banco
                           on conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                          and conta_corrente.cod_agencia = plano_banco.cod_agencia
                          and conta_corrente.cod_banco = plano_banco.cod_banco
                          and plano_banco.exercicio = lote.exercicio
                        where pagamento_lote.cod_lote  = lote.cod_lote
                          and pagamento_lote.exercicio = lote.exercicio
                      ) as situacao_plano_banco
             from arrecadacao.lote
    ";

    return $stSql;
}

}
?>
