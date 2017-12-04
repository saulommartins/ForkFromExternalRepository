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
    * Classe de mapeamento da tabela folhapagamento.configuracao_empenho_evento
    * Data de Criação: 28/11/2008

    * @author Analista     : Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TFolhaPagamentoConfiguracaoEmpenhoEvento.class.php 63259 2015-08-10 14:30:00Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TFolhaPagamentoConfiguracaoEmpenhoEvento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela("folhapagamento.configuracao_empenho_evento");
    
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_configuracao,sequencia,cod_evento,timestamp');
    
        $this->AddCampo('exercicio'       ,'char'     ,true  ,'4'  ,true,'TFolhaPagamentoConfiguracaoEmpenho');
        $this->AddCampo('cod_configuracao','integer'  ,true  ,''   ,true,'TFolhaPagamentoConfiguracaoEmpenho');
        $this->AddCampo('sequencia'       ,'integer'  ,true  ,''   ,true,'TFolhaPagamentoConfiguracaoEmpenho');
        $this->AddCampo('timestamp'       ,'timestamp',true  ,''   ,true,'TFolhaPagamentoConfiguracaoEmpenho');
        $this->AddCampo('cod_evento'      ,'integer'  ,true  ,''   ,true,'TFolhaPagamentoEvento');
    }

    /**
        * Método Destruct
        * @access Private
    */
    public function __destruct() {}
}
?>
