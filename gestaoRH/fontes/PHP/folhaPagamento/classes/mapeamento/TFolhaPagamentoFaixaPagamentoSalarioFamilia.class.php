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
  * Classe de mapeamento da tabela FOLHAPAGAMENTO.FAIXA_PAGAMENTO_SALARIO_FAMILIA
  * Data de Criação: 19/04/2006

  * @author Analista: Vandre Miguel Ramos
  * @author Desenvolvedor: Andre Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.05.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela FOLHAPAGAMENTO.FAIXA_PAGAMENTO_SALARIO_FAMILIA
  * Data de Criação: 19/04/2006

  * @author Analista: Vandre Miguel Ramos
  * @author Desenvolvedor: Andre Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoFaixaPagamentoSalarioFamilia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TFolhaPagamentoFaixaPagamentoSalarioFamilia()
    {
        parent::Persistente();
        $this->setTabela('folhapagamento.faixa_pagamento_salario_familia');

        $this->setCampoCod('cod_regime_previdencia');
        $this->setComplementoChave('');

        $this->AddCampo('cod_faixa'             , 'integer'  , true, ''    ,  true, false );
        $this->AddCampo('timestamp'             , 'timestamp', true, ''    ,  true,  true );
        $this->AddCampo('cod_regime_previdencia', 'integer'  , true, ''    ,  true,  true );
        $this->AddCampo('vl_inicial'            , 'numeric'  , true, '14,2', false, false );
        $this->AddCampo('vl_final'              , 'numeric'  , true, '14,2', false, false );
        $this->AddCampo('vl_pagamento'          , 'numeric'  , true, '14,2', false, false );

    }

}
