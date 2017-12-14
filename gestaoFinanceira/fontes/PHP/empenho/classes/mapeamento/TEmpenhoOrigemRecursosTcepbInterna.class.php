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
    * Classe de mapeamento da tabela EMPENHO.ORIGEM_RECURSOS_TCEPB_INTERNA
    * Data de Criação: 14/05/2008

    * @author Analista: Gelson W. Golçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TEmpenhoOrigemRecursosTcepbInterna.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.05
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  EMPENHO.ORIGEM_RECURSOS_TCEPB_INTERNA
  * Data de Criação: 14/05/2008

  * @author Analista: Gelson W. Golçalves
  * @author Desenvolvedor: Henrique Girardi dos Santos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoOrigemRecursosTcepbInterna extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function TEmpenhoOrigemRecursosTcepbInterna()
    {
        parent::Persistente();
        $this->setTabela('empenho.origem_recursos_tcepb_interna');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_origem_recursos');

        $this->AddCampo( 'cod_origem_recursos','integer',false, true, ''   ,true  );
        $this->AddCampo( 'nome'               ,'char'   ,false, true, '200',false );

    }
}
