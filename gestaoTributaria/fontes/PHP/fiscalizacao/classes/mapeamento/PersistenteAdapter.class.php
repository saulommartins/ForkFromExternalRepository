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
 * Classe adaptadora de Persistente
 * Data de Criação: 19/11/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Marcio Medeiros <marcio.medeiros@cnm.org.br>

 * @package URBEM
 * @subpackage Mapeamento

 * $Id: PersistenteAdapter.class.php 59612 2014-09-02 12:00:51Z gelson $
 */
class PersistenteAdapter extends Persistente
{
    /**
     * Método construtor
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Verifica a existência de campo do tipo serial
     * Se for inclusão, o campo serial é excluído de arEstrutura;
     * Se alteração ou exclusão, o serial é mantido e seu tipo é
     * alterado para 'integer', como é esperado pela Persistente.
     *
     * @param  bool $boInclusao
     * @return void
     */
    private function checarSerial($boInclusao)
    {
        $reordenar = false;
        $i = 0;
        foreach ($this->arEstrutura as $item) {
            if ($item->stTipoCampo == 'serial') {
                if ($boInclusao) {
                    // Retira atributo serial
                    unset($this->arEstrutura[$i]);
                    $reordenar = true;
                } else {
                    // Mantem serial e altera para tipo correto
                    $item->stTipoCampo = 'integer';
                }
            }
            $i++;
        }
        if ($reordenar) {
            // Item serial foi excluído, reordenar os índices do array
            $this->arEstrutura = array_values($this->arEstrutura);
        }
    }

    public function inclusao()
    {
        $this->checarSerial(true);

        return parent::inclusao();
    }

    public function alteracao()
    {
        $this->checarSerial(false);

        return parent::alteracao();
    }

    public function exclusao()
    {
        $this->checarSerial(false);

        return parent::exclusao();
    }

}
?>
