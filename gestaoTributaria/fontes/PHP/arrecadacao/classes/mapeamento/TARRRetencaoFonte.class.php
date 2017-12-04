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
    * Classe de mapeamento da tabela ARRECADACAO.RETENCAO_FONTE
    * Data de Criação: 23/10/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRRetencaoFonte.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.22
*/

/*
$Log$
Revision 1.2  2006/10/30 11:23:00  cercato
setando paramentro "requerido" como false para o timestamp.

Revision 1.1  2006/10/26 14:06:43  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRRetencaoFonte extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TARRRetencaoFonte()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.retencao_fonte');

        $this->setCampoCod('cod_retencao');
        $this->setComplementoChave('cod_retencao,inscricao_economica,timestamp');

        $this->AddCampo('cod_retencao', 'integer', true, '', true, false );
        $this->AddCampo('inscricao_economica', 'integer', true, '', true, true  );
        $this->AddCampo('timestamp', 'timestamp', false, '', true, true );
        $this->AddCampo('valor_retencao', 'numeric', true, '14,2', false, false );
    }

}
?>
