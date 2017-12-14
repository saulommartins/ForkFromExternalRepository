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
  * Mapeamento tcmba.conciliacao_lancamento_arrecadacao
  * Data de Criação: 09/06/2016
  * 
  * @author Analista: Valtair
  * @author Desenvolvedor: Michel Teixeira
  *
  * $Id: TTCMBAConciliacaoLancamentoArrecadacaoEstornada.class.php 65704 2016-06-09 14:33:25Z michel $
*/
require_once CLA_PERSISTENTE;

class TTCMBAConciliacaoLancamentoArrecadacaoEstornada extends Persistente {

    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.conciliacao_lancamento_arrecadacao_estornada');

        $this->setCampoCod('cod_plano');
        $this->setComplementoChave('exercicio,mes,exercicio_conciliacao,timestamp_arrecadacao,cod_arrecadacao,timestamp_estornada,tipo');

        $this->AddCampo('cod_plano'             , 'integer'  , true, ''     , true  , true );
        $this->AddCampo('exercicio'             , 'char'     , true, '04'   , true  , true );
        $this->AddCampo('mes'                   , 'integer'  , true, ''     , true  , true );
        $this->AddCampo('exercicio_conciliacao' , 'char'     , true, '04'   , true  , true );
        $this->AddCampo('timestamp_arrecadacao' , 'timestamp', true, ''     , true  , true );
        $this->AddCampo('cod_arrecadacao'       , 'integer'  , true, ''     , true  , true );
        $this->AddCampo('timestamp_estornada'   , 'timestamp', true, ''     , true  , true );
        $this->AddCampo('tipo'                  , 'char'     , true, '01'   , true  , true );
        $this->AddCampo('cod_tipo_conciliacao'  , 'integer'  , true, ''     , true  , true );
    } 
}