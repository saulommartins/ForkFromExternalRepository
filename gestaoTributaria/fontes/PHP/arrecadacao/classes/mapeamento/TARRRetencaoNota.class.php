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
    * Classe de mapeamento da tabela ARRECADACAO.RETENCAO_NOTA
    * Data de Criação: 23/10/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRRetencaoNota.class.php 59612 2014-09-02 12:00:51Z gelson $

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

class TARRRetencaoNota extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TARRRetencaoNota()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.retencao_nota');

        $this->setCampoCod('cod_nota');
        $this->setComplementoChave('cod_nota,cod_retencao,inscricao_economica,timestamp,cod_municipio,cod_uf,numcgm_retentor');

        $this->AddCampo('cod_nota', 'integer', true, '', true, false );
        $this->AddCampo('inscricao_economica', 'integer', true, '', true, true  );
        $this->AddCampo('timestamp', 'timestamp', false, '', true, true );
        $this->AddCampo('cod_retencao', 'integer', true, '', true, true );
        $this->AddCampo('cod_municipio', 'integer', true, '', false, true );
        $this->AddCampo('cod_uf', 'integer', true, '', false, true );
        $this->AddCampo('numcgm_retentor', 'integer', true, '', false, true );
        $this->AddCampo('num_serie', 'varchar', true, '10', false, false );
        $this->AddCampo('num_nota', 'integer', true, '', false, false );
        $this->AddCampo('dt_emissao', 'date', true, '', false, false );
        $this->AddCampo('valor_nota', 'numeric', true, '14,2', false, false );
    }

}
?>
