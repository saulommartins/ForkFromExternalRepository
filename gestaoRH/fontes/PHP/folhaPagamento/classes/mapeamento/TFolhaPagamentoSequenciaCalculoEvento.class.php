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
    * Classe de mapeamento da tabela FOLHAPAGAMENTO.SEQUENCIA_CALCULO_EVENTO
    * Data de Criação: 24/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Eduardo Antunez

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 32866 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.SEQUENCIA_CALCULO_EVENTO
  * Data de Criação: 24/11/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Eduardo Antunez

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoSequenciaCalculoEvento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TFolhaPagamentoSequenciaCalculoEvento()
    {
        parent::Persistente();
        $this->setTabela('folhapagamento.sequencia_calculo_evento');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_sequencia,cod_evento');

        $this->AddCampo('cod_sequencia','integer',true,'',true,true);
        $this->AddCampo('cod_evento','integer',true,'',true,true);

    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = " SELECT evento.codigo                                                            \n";
        $stSql .= "      , evento.descricao                                                         \n";
        $stSql .= "      , sequencia_calculo.cod_sequencia                                          \n";
        $stSql .= "      , sequencia_calculo.sequencia                                              \n";
        $stSql .= "      , evento.evento_sistema                                                    \n";
        $stSql .= "   FROM folhapagamento.evento                           \n";
        $stSql .= "      , folhapagamento.sequencia_calculo_evento         \n";
        $stSql .= "      , folhapagamento.sequencia_calculo                \n";
        $stSql .= "  WHERE evento.cod_evento = sequencia_calculo_evento.cod_evento                  \n";
        $stSql .= "    AND sequencia_calculo_evento.cod_sequencia = sequencia_calculo.cod_sequencia \n";

        return $stSql;
    }
}
