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
 * Controle de Erros
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Santos <fellipe.santos>
 * @package gestaoFinanceira
 * @subpackage LDO
 */

/**
 * Classe de Manipulação de Erro
 * @author Fellipe Santos <fellipe.santos>
 * @package gestaoFinanceira
 * @subpackage LDO
 */
class X9Erro
{
    # Erros que geram mensagens de email.
    public static $error_email  = array('Undefined variable',
                                        'Missing argument',
                                        'array_push()'
                                        );

    # Erros que criam ticket.
    public static $error_ticket = array('mysql_fetch_array()',
                                        'erro4',
                                        'teste_de_ticket'
                                        );

    public function __construct()
    {
        //Não implementada.
    }

    public static function executar(X9 $obX9)
    {
        foreach (self::$error_email as $email) {
            if (preg_match("/($email)/i", $obX9->getErro(), $match_email))
                break;

        }

        foreach (self::$error_ticket as $ticket) {
            if (preg_match("/($ticket)/i", $obX9->getErro(), $match_ticket))
                break;
        }

        if ($match_email) {
            //$obX9->enviarEmail();
        }

        $obX9->enviarTicket();
        if ($match_ticket) {
            //$obX9->enviarTicket();
        }

        if (!($match_email || $match_ticket)) {
            //$obX9->enviarEmail();
        }
    }
}

/**
 * Classe de Geração de Ticket
 * @author Fellipe Santos <fellipe.santos>
 * @package gestaoFinanceira
 * @subpackage LDO
 */
class X9
{
    private $stTitulo;
    private $stDescricao;
    private $stUsuario;
    private $stErro;
    private $stSubpacote;
    private $stPacote;
    private $stUC;

    public function setTitulo($stTitulo)
    {
        $this->stTitulo = $stTitulo;
    }

    public function getTitulo()
    {
        return $this->stTitulo;
    }

    public function setDescricao($stDescricao)
    {
        $this->stDescricao = $stDescricao;
    }

    public function getDescricao()
    {
        return $this->stDescricao;
    }

    public function setErro($stErro)
    {
        $this->stErro = $stErro;
    }

    public function getErro()
    {
     return $this->stErro;
    }

    public function setPacote($stPacote)
    {
        $this->stPacote = $stPacote;
    }

    public function getPacote()
    {
        return $this->stPacote;
    }

    public function setSubpacote($stSubpacote)
    {
        $this->stSubpacote = $stSubpacote;
    }

    public function getSubPacote()
    {
        return $this->stSubpacote;
    }

    public function setAutor($stAutor)
    {
        $this->stAutor = $stAutor;
    }

    public function getAutor()
    {
        return $this->stAutor;
    }

    public function setUsuario($stUsuario)
    {
        $this->stUsuario = $stUsuario;
    }

    public function getUsuario()
    {
        return $this->stUsuario;
    }

    public function setUC($stUC)
    {
        $this->stUC = $stUC;
    }

    public function getUC()
    {
        return $this->stUC;
    }

    public function enviarEmail()
    {
        $stHeaders  = 'MIME-Version: 1.0' . "\r\n";
        $stHeaders .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $stHeaders .= 'Cc: heleno.santos@cnm.org.br' . "\r\n";
        $stHeaders .= 'Bcc: bruno.ferreira@cnm.org.br' . "\r\n";

        mail($this->getUsuario() . '@cnm.org.br', $this->getTitulo(), $this->getDescricao(), $stHeaders);
    }

    public function capturaTela()
    {
        echo '<pre>capturaTela</pre>';
        $stJS = <<<EOJS
            var o = parent.frames['oculto'].document;
            var applet = d.createElement('applet');

            applet.setAttribute('code', 'br.org.cnm.urbem.CapturaTela');
            applet.setAttribute('archive', '../../Sx9.jar');
            applet.setAttribute('width', 1);
            applet.setAttribute('height', 1);
            //applet.setAttribute('visibility', 'hidden');
            d.body.appendChild(applet);
EOJS;

        SistemaLegado::executaFrameOculto($stJS);
    }

    public function enviarTicket()
    {
        $this->capturaTela();

        return;

        $arGestoes = array
        (
            'GA'  => 'Administrativa',
            'GRH' => 'Recursos Humanos',
            'GT'  => 'Tributaria',
            'GF'  => 'Financeira',
            'GP'  => 'Patrimonial',
            'FW'  => 'Framework',
            'GPC' => 'Prestacao de Contas'
        );

        # recuperar __FORM_TOKEN
        $stUrl   = 'http://trac.sw.cnm.org.br';
        $rsToken = $this->recuperarUrl($stUrl);
        $stToken = $this->recuperarTag($rsToken, 'name="__FORM_TOKEN" value="', '" />');

        # autenticar login
        if ($stToken[0]) {
            $stUrl = 'http://trac.sw.cnm.org.br/login';
            $arParametros = array();
            $arParametros['user']         = 'fellipe.santos';
            $arParametros['password']     = 'ttysw67';
            $arParametros['refer']        = 'refer';
            $arParametros['__FORM_TOKEN'] = $stToken[0];

            $rsLogin  = $this->executarUrl($stUrl, $arParametros);
        }

        # recuperar __FORM_TOKEN
        $stUrl    = 'http://trac.sw.cnm.org.br/newticket';
        $rsToken  = $this->recuperarUrl($stUrl);
        $stToken  = $this->recuperarTag($rsToken, 'name="__FORM_TOKEN" value="', '" />');

        # cadastrar ticket
        $arParametros = array();
        $arParametros['field_summary']       = $this->getTitulo();
        $arParametros['field_description']   = "{{{\n#!html\n" . $this->getDescricao() . "\n}}}";
        $arParametros['field_owner']         = $this->getUsuario();
        $arParametros['field_type']          = 'bug';
        $arParametros['field_priority']      = 5;
        $arParametros['field_milestone']     = strtoupper($this->getPacote()) . '_' . strtoupper($this->getSubPacote());
        $arParametros['field_component']     = str_replace(' - ', ' ', $this->getUC());
        $arParametros['field_version']       = '';
        $arParametros['field_severity']      = 'Alta';
        $arParametros['field_keywords']      = '';
        $arParametros['field_cc']            = '';
        $arParametros['field_blockedby']     = '';
        $arParametros['field_modulo']        = substr($this->getUC(), 0, 5) . ' - ' . strtoupper($this->getSubPacote());
        $arParametros['field_causa_raiz']    = 'Erro de Programacao';
        $arParametros['field_estimatedhour'] = 0;
        $arParametros['field_blocking']      = '';
        $arParametros['field_projeto']       = 'Urbem';
        $arParametros['field_gestao']        = $arGestoes[strtoupper($this->getPacote())];
        $arParametros['field_total_dias']    = 0;
        $arParametros['field_status']        = 'assigned';
        $arParametros['submit']              = 'Create ticket';
        $arParametros['__FORM_TOKEN']        = $stToken[0];

        $stUrl    = 'http://trac.sw.cnm.org.br/newticket';
        $rsTicket = $this->executarUrl($stUrl, $arParametros, true);
        $inTicket = $this->recuperarTag($rsTicket, 'name="ticket" value="', '" />');

        return $inTicket[0];
    }

    public function anexarImagem($inTicket = '14586')
    {
        # recuperar __FORM_TOKEN
        $stUrl   = 'http://trac.sw.cnm.org.br/attachment/ticket/' . $inTicket . '/?action=new&attachfilebutton=Attach+file';
        $rsToken = $this->recuperarUrl($stUrl);
        $stToken = $this->recuperarTag($rsToken, 'name="__FORM_TOKEN" value="', '" />');

        # cadastrar anexo
        $arParametros = array();

        $arParametros['attachment']   = '@/home/fellipe/anexo_1.jpeg';
        $arParametros['description']  = 'teste1';
        $arParametros['replace']      = 'on';
        $arParametros['action']       = 'new';
        $arParametros['realm']        = 'ticket';
        $arParametros['submit']       = 'Add attachment';
        $arParametros['id']           = $inTicket;
        $arParametros['__FORM_TOKEN'] = $stToken[0];

        $stUrl   = 'http://trac.sw.cnm.org.br/attachment/ticket/' . $inTicket . '/?action=new&attachfilebutton=Attach+file';
        $rsTicket = $this->executarUrl($stUrl, $arParametros, true);
    }

    private function executarUrl($stUrl, $arParametros = null, $boArray = false)
    {
        $stParametros = null;

        if ($boArray == false) {
            foreach ($arParametros as $inChave => $stValor)
                $stParametros .= '&' . $inChave . '=' . $stValor;
        } else {
            $stParametros = $arParametros;
        }

        $obCurl = curl_init($stUrl);

        curl_setopt($obCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($obCurl, CURLOPT_VERBOSE, 1);
        curl_setopt($obCurl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($obCurl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($obCurl, CURLOPT_COOKIEJAR, '/tmp/x9_cookie');
        curl_setopt($obCurl, CURLOPT_COOKIEFILE, '/tmp/x9_cookie');
        curl_setopt($obCurl, CURLOPT_POST, 1);
        curl_setopt($obCurl, CURLOPT_POSTFIELDS, $stParametros);

        $rsUrl = curl_exec($obCurl);

        curl_close($obCurl);

        return $rsUrl;
    }

    private function recuperarUrl($stUrl)
    {
        $obCurl = curl_init($stUrl);

        curl_setopt($obCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($obCurl, CURLOPT_VERBOSE, 1);
        curl_setopt($obCurl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($obCurl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($obCurl, CURLOPT_COOKIEJAR, '/tmp/x9_cookie');
        curl_setopt($obCurl, CURLOPT_COOKIEFILE, '/tmp/x9_cookie');

        $rs = curl_exec($obCurl);

        curl_close($obCurl);

        return $rs;
    }

    private function recuperarTag($stTexto, $stInicio, $stFinal)
    {
        $inTamanhoInicio = strlen($stInicio);
        $inTamanhoFinal  = strlen($stFinal);

        while (($inIndice = strpos($stTexto, $stInicio, $inIndice)) !== false) {
            $inIndice += $inTamanhoInicio;

            if (($inPosicaoFinal = strpos($stTexto, $stFinal, $inIndice)) !== false) {
                $rsTag[] = substr($stTexto, $inIndice, $inPosicaoFinal - $inIndice);
                $inIndice = $inPosicaoFinal + $finalTamanhoTag;
            }
        }

        return $rsTag;
    }
}
