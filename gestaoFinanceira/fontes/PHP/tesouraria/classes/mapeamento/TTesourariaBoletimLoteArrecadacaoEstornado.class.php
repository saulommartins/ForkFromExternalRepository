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
* Classe de mapeamento da tabela TESOURARIA.BOLETIM_LOTE
* Data de Criação: 27/02/2007
* @author Analista: Gelson W. Gonçalves
* @author Desenvolvedor: Henrique Boaventura
* @package URBEM
* @subpackage

$Revision: 30668 $
$Name$
$Author: hboaventura $
$Date: 2007-07-03 17:17:00 -0300 (Ter, 03 Jul 2007) $

* Casos de uso: uc-02.04.33

*/

/*

$Log$
Revision 1.1  2007/07/03 20:16:49  hboaventura
uc-02.04.33

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTesourariaBoletimLoteArrecadacaoEstornado extends Persistente
{
    public function TTesourariaBoletimLoteArrecadacaoEstornado()
    {
        parent::Persistente();
        $this->setTabela("tesouraria.boletim_lote_arrecadacao_estornado");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_entidade, cod_boletim, cod_lote, timestamp_arrecadacao, cod_arrecadacao');

        $this->AddCampo('cod_arrecadacao'       , 'integer'     , true, ''  , true  , true );
        $this->AddCampo('timestamp_arrecadacao' , 'timestamp'   , true, ''  , true  , true );
        $this->AddCampo('cod_lote'              , 'integer'     , true, ''  , true  , true );
        $this->AddCampo('cod_entidade'          , 'integer'     , true, ''  , true  , true );
        $this->AddCampo('cod_boletim'           , 'integer'     , true, ''  , true  , true );
        $this->AddCampo('exercicio'             , 'char'        , true, '4' , true  , true );
        $this->AddCampo('timestamp_anulacao'    , 'timestamp'   , true, ''  , true  , true );

    }

    public function recuperaBoletimLoteEstornado(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaBoletimLoteEstornado",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    }

    public function montaRecuperaBoletimLoteEstornado()
    {
        $stSQL = "
        select lote.cod_lote
             , lote.data_lote
             , to_char(lote.data_lote , 'dd/mm/YYYY') as data_br
             , lote.cod_banco
             , lote.cod_agencia
             , lote.exercicio
             , ( select num_agencia || ' - ' || nom_agencia
                   from monetario.agencia
                  where agencia.cod_agencia = lote.cod_agencia
                    and agencia.cod_banco = lote.cod_banco
               ) as agencia
             , ( select num_banco || ' - ' || nom_banco
                   from monetario.banco
                  where banco.cod_banco = lote.cod_banco
               ) as banco
          from arrecadacao.lote
         where ( exists ( select 1
                            from tesouraria.boletim_lote_arrecadacao
                           where boletim_lote_arrecadacao.cod_lote = lote.cod_lote
                             and boletim_lote_arrecadacao.exercicio = lote.exercicio
                             and not exists ( select 1
                                                from tesouraria.boletim_lote_arrecadacao_estornado
                                               where boletim_lote_arrecadacao_estornado.cod_lote = boletim_lote_arrecadacao.cod_lote
                                                 and boletim_lote_arrecadacao_estornado.exercicio = boletim_lote_arrecadacao.exercicio
                                                 and boletim_lote_arrecadacao_estornado.cod_entidade = boletim_lote_arrecadacao.cod_entidade
                                                 and boletim_lote_arrecadacao_estornado.cod_arrecadacao = boletim_lote_arrecadacao.cod_arrecadacao
                                                 and boletim_lote_arrecadacao_estornado.timestamp_arrecadacao = boletim_lote_arrecadacao.timestamp_arrecadacao
                                            )
                             and boletim_lote_arrecadacao.cod_entidade = ".$this->getDado('cod_entidade')."
                             and boletim_lote_arrecadacao.cod_boletim = ".$this->getDado('cod_boletim')."
                             and boletim_lote_arrecadacao.exercicio = '".$this->getDado('exercicio')."'
                        )
                 or
                 exists ( select 1
                            from tesouraria.boletim_lote_transferencia
                      inner join tesouraria.transferencia
                              on transferencia.cod_lote = boletim_lote_transferencia.cod_lote
                             and transferencia.cod_entidade = boletim_lote_transferencia.cod_entidade
                             and transferencia.exercicio = boletim_lote_transferencia.exercicio
                             and transferencia.tipo = boletim_lote_transferencia.tipo
                           where boletim_lote_transferencia.cod_lote_arrecadacao = lote.cod_lote
                             and boletim_lote_transferencia.exercicio = lote.exercicio
                             and not exists ( select 1
                                                from tesouraria.boletim_lote_transferencia_estornada
                                               where boletim_lote_transferencia_estornada.cod_lote = boletim_lote_transferencia.cod_lote
                                                 and boletim_lote_transferencia_estornada.exercicio = boletim_lote_transferencia.exercicio
                                                 and boletim_lote_transferencia_estornada.cod_lote_arrecadacao = boletim_lote_transferencia.cod_lote_arrecadacao
                                                 and boletim_lote_transferencia_estornada.cod_entidade = boletim_lote_transferencia.cod_entidade
                                                 and boletim_lote_transferencia_estornada.tipo = boletim_lote_transferencia.tipo
                                            )
                             and boletim_lote_transferencia.cod_entidade = ".$this->getDado('cod_entidade')."
                             and boletim_lote_transferencia.exercicio = '".$this->getDado('exercicio')."'
                             and transferencia.cod_boletim = ".$this->getDado('cod_boletim')."
                        )
              )
        ";

        $stSQL.= "
      group by  lote.cod_lote, lote.cod_banco, lote.cod_agencia, lote.exercicio, lote.data_lote
        ";

        return $stSQL;

    }

}
?>
