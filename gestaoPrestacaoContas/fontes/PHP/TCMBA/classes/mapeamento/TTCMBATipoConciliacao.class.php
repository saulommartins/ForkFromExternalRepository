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
  * Mapeamento tcmba.tipo_conciliacao_lancamento_contabil
  * Data de Criação: 13/04/2016
  * 
  * @author Analista: Valtair
  * @author Desenvolvedor: Carlos Adriano
  *
  * $Id: TTCMBATipoConciliacao.class.php 65436 2016-05-20 20:27:14Z carlos.silva $
  * $Revision: $
  * $Author: $
  * $Date: $
*/
require_once CLA_PERSISTENTE;

class TTCMBATipoConciliacao extends Persistente {

    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.tipo_conciliacao');
        $this->setComplementoChave('cod_tipo_conciliacao');

        $this->AddCampo('cod_tipo_conciliacao', 'integer',  true, '', true, true);
        $this->AddCampo('descricao', 'varchar', true, '100', true, true);
    }
}